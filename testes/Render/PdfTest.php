<?php

namespace Sped\Gnre\Test\Render;

use PHPUnit\Framework\TestCase;
use Dompdf\Dompdf;
use Sped\Gnre\Render\Html;
use Sped\Gnre\Render\Pdf;

/**
 * @covers \Sped\Gnre\Render\Pdf
 */
class PdfTest extends TestCase
{
    public function testDeveCriarOpdfApartirDoHtml()
    {
        $dom = $this->createMock('\Dompdf\Dompdf');

        $html = $this->createMock('\Sped\Gnre\Render\Html');

        $pdf = $this->createMock('\Sped\Gnre\Render\Pdf');
        $pdf->expects($this->once())
                ->method('create')
                ->will($this->returnValue($dom));

        $domPdf = $pdf->create($html);

        $this->assertInstanceOf('\Dompdf\Dompdf', $domPdf);
    }

    public function testDeveRetornarUmaInstanciaDoDomPdf()
    {
        $dom = new CoveragePdf();
        $this->assertInstanceOf('\Dompdf\Dompdf', $dom->getDomPdf());
    }

    /**
     * Regression gate for dompdf 3.1.6 (QA #1): Pdf::create must call
     * loadHtml + render and emit a real PDF byte stream.
     */
    public function testCreateGeraPdfRealComDompdf31()
    {
        $html = $this->createMock(Html::class);
        $html->expects($this->once())
            ->method('getHtml')
            ->willReturn('<html><body><h1>GNRE guia</h1><p>UF=SP valor=10.00</p></body></html>');

        $pdf = new Pdf();
        $dompdf = $pdf->create($html);

        $this->assertInstanceOf(Dompdf::class, $dompdf);
        $output = $dompdf->output();
        $this->assertIsString($output);
        $this->assertGreaterThan(500, strlen($output));
        $this->assertSame('%PDF-', substr($output, 0, 5));
        $this->assertTrue(method_exists($dompdf, 'loadHtml'));
    }
}

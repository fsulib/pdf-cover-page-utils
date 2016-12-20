#!/usr/bin/env php
<?php

require_once(__DIR__ . '/tfpdf/tfpdf.php');

// map FPDF to tFPDF so FPDF_TPL can extend it
class FPDF extends tFPDF
{
    /**
     * "Remembers" the template id of the imported page
     */
    protected $_tplIdx;

    /**
     * Draw an imported PDF logo on every page
     */
    public function Header()
    {
        if (is_null($this->_tplIdx)) {
            $this->setSourceFile(__DIR__ . '/coverpage.pdf');
            $this->_tplIdx = $this->importPage(1);
        }
        /* The rest of this function was originally included but is not useful for our purposes
        
        $size = $this->useTemplate($this->_tplIdx, 130, 5, 60);

        $this->SetFont('Times', '', 16);
        $this->SetTextColor(0);
        $this->SetXY($this->lMargin, 5);

        $text = 'tFPDF (v' . tFPDF_VERSION . ') and FPDI (v'
            . FPDI::VERSION . ')';
        $this->Cell(0, $size['h'], $text);
        $this->Ln(); */
    }
}

require_once(__DIR__ . '/FPDI-1.6.1/fpdi.php');

ini_set('display_errors',1);  
error_reporting(E_ALL);

# Arg 1 is MODS record
if (empty($argv[1])) {
  echo "Missing arg 1\n";
  exit();
}

# Arg 2 is output file
if (empty($argv[2])) {
  echo "Missing arg 2\n";
  exit();
}

$mods = simplexml_load_file($argv[1]);

// Title interpolation
$preppedNonSort = ($mods->titleInfo->nonSort ? "{$mods->titleInfo->nonSort} " : "");
$preppedSubTitle = ($mods->titleInfo->subTitle ? ": {$mods->titleInfo->subTitle}" : "");
$preppedTitle = "{$preppedNonSort}{$mods->titleInfo->title}{$preppedSubTitle}";

// Build array of authors
unset($author_array);
$author_array = array();
foreach ($mods->name as $author) {
  $preppedAuthor = "{$author->namePart[0]} {$author->namePart[1]}";
  $author_array[] = $preppedAuthor;
}
if (count($author_array) == 1) {                                          
  $preppedAuthors = implode("", $author_array);                                
}                                                                            
elseif (count($author_array) == 2) {                                      
  $preppedAuthors = implode(" and ", $author_array);                           
}                                                                            
else {                                                                       
  $preppedAuthors = implode(", ", array_slice($author_array, 0, -2)) . ", " . implode(" and ", array_slice($author_array, -2)); 
}  

// Get publication date
$pubDate = $mods->originInfo->dateIssued;
$pubnote = "";
// Get publication note
foreach ($mods->note as $note) {
  if ($note['displayLabel'] == "Publication Note") {
    $pubNote = $note;
  }
}

// Add coverpage
$pdf = new FPDI();
$pdf->AddPage('P', 'Letter');
//$pdf->setSourceFile(__DIR__. "/coverpage.pdf");
 $tplIdx = $pdf->importPage(1);
 $pdf->useTemplate($tplIdx);
 $pdf->SetTextColor(0, 0, 0);
// $pdf->SetFont('Times');


// Add some Unicode font (uses UTF-8)
$pdf->AddFont('DejaVuSerif', '', 'DejaVuSerif.ttf', true);
$pdf->SetFont('DejaVuSerif', '');

$pdf->setFontSize(14);
$pdf->SetXY(25, 55);
$pdf->Write(0, $pubDate);

$pdf->setFontSize(26);
$pdf->SetXY(25, 60);
$pdf->MultiCell(175, 10, $preppedTitle, 0, 'L');

$pdf->setFontSize(14);
$pdf->setLeftMargin(25);
$pdf->SetY($pdf->GetY() + 3);
$pdf->MultiCell(0, 5, $preppedAuthors, 0, 'L');

if($pubnote){
$pdf->setFontSize(8);
$pdf->SetY($pdf->GetY() + 5);
$pdf->MultiCell(175, 5, $pubNote, 0, 'L');
}

$pdf->Output($argv[2], 'F'); // tFPDF switches the argument parameters for Output();

?>
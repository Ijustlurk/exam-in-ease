<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$templatePath = 'public/templates/Exam Template.docx';
$phpWord = IOFactory::load($templatePath);

// Get sections
foreach ($phpWord->getSections() as $sectionIndex => $section) {
    echo "=== SECTION $sectionIndex ===\n\n";
    
    foreach ($section->getElements() as $element) {
        $elementClass = get_class($element);
        echo "Element Type: " . $elementClass . "\n";
        
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            echo "Text: " . $element->getText() . "\n";
            echo "Font: " . ($element->getFontStyle() ? json_encode($element->getFontStyle()) : 'null') . "\n";
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            echo "TextRun:\n";
            foreach ($element->getElements() as $textElement) {
                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                    echo "  - " . $textElement->getText() . "\n";
                }
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            echo "Table found\n";
            $rows = $element->getRows();
            foreach ($rows as $row) {
                echo "  Row:\n";
                foreach ($row->getCells() as $cell) {
                    echo "    Cell: ";
                    foreach ($cell->getElements() as $cellElement) {
                        if ($cellElement instanceof \PhpOffice\PhpWord\Element\Text) {
                            echo $cellElement->getText() . " ";
                        } elseif ($cellElement instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($cellElement->getElements() as $te) {
                                if ($te instanceof \PhpOffice\PhpWord\Element\Text) {
                                    echo $te->getText() . " ";
                                }
                            }
                        }
                    }
                    echo "\n";
                }
            }
        }
        echo "\n";
    }
}

// Save as HTML for easier viewing
$htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
$htmlWriter->save('storage/app/template_preview.html');
echo "\n=== HTML version saved to storage/app/template_preview.html ===\n";

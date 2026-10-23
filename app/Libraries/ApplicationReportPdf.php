<?php

namespace App\Libraries;

class ApplicationReportPdf
{
    private const PAGE_WIDTH = 612;
    private const PAGE_HEIGHT = 936;
    private const LEFT = 28;
    private const TABLE_WIDTH = 556;
    private const COLUMNS = [24, 42, 104, 104, 120, 78, 84];

    public function render(array $applications, array $metadata): string
    {
        $pages = [];
        $pageContent = '';
        $pageNumber = 0;
        $y = 0;

        foreach ($applications as $index => $application) {
            if ($pageContent === '' || $y < 94) {
                if ($pageContent !== '') {
                    $pages[] = $this->finishPage($pageContent, $pageNumber);
                }
                $pageNumber++;
                [$pageContent, $y] = $this->startPage($metadata, $pageNumber);
            }

            $cells = [
                (string) ($index + 1),
                '#' . (int) $application['id'],
                trim((string) ($application['applicant_name'] ?? '')) ?: 'Applicant',
                (string) ($application['scholarship_title'] ?? ''),
                trim((string) ($application['email'] ?? '') . "\n" . (string) ($application['mobile_number'] ?? '')),
                (string) ($application['created_at'] ?? ''),
                ucwords(str_replace('_', ' ', (string) ($application['status'] ?? 'pending'))),
            ];
            $wrapped = [];
            $lineCount = 1;
            foreach ($cells as $column => $value) {
                $wrapped[$column] = $this->wrap($value, self::COLUMNS[$column] - 9, 8);
                $lineCount = max($lineCount, count($wrapped[$column]));
            }
            $rowHeight = max(25, 10 + $lineCount * 10);
            if ($y - $rowHeight < 68) {
                $pages[] = $this->finishPage($pageContent, $pageNumber);
                $pageNumber++;
                [$pageContent, $y] = $this->startPage($metadata, $pageNumber);
            }

            $pageContent .= $this->rect(self::LEFT, $y - $rowHeight, self::TABLE_WIDTH, $rowHeight, ($index % 2) === 0 ? [1, 1, 1] : [.965, .976, .969], [.78, .82, .82]);
            $x = self::LEFT;
            foreach (self::COLUMNS as $column => $width) {
                if ($column > 0) {
                    $pageContent .= $this->line($x, $y, $x, $y - $rowHeight, [.78, .82, .82]);
                }
                foreach ($wrapped[$column] as $lineIndex => $line) {
                    $pageContent .= $this->text($x + 5, $y - 13 - ($lineIndex * 10), $line, 8, $column === 0 || $column === 1 ? 'F2' : 'F1', [.22, .32, .34]);
                }
                $x += $width;
            }
            $y -= $rowHeight;
        }

        if ($pageContent === '') {
            $pageNumber = 1;
            [$pageContent, $y] = $this->startPage($metadata, $pageNumber);
        }
        if ($applications === []) {
            $pageContent .= $this->rect(self::LEFT, $y - 30, self::TABLE_WIDTH, 30, [1, 1, 1], [.78, .82, .82]);
            $pageContent .= $this->text(self::LEFT + 8, $y - 18, 'No applications match the selected filters.', 8, 'F1', [.40, .49, .51]);
        }
        $pages[] = $this->finishPage($pageContent, $pageNumber);

        $logo = isset($metadata['logo_path']) ? $this->loadPngLogo((string) $metadata['logo_path']) : null;

        return $this->buildPdf($pages, $logo);
    }

    private function startPage(array $metadata, int $pageNumber): array
    {
        $content = '';
        $content .= "q 38 0 0 38 28 856 cm /Logo Do Q\n";
        $content .= $this->text(72, 883, 'MUNICIPALITY OF SANTA IGNACIA', 11, 'F2', [.12, .25, .28]);
        $content .= $this->text(72, 868, 'MUNICIPAL SCHOLARSHIP PROGRAM', 8, 'F1', [.36, .45, .47]);
        $content .= $this->text(480, 883, 'APPLICATION REPORT', 7, 'F2', [.24, .39, .41]);
        $content .= $this->line(self::LEFT, 848, self::LEFT + self::TABLE_WIDTH, 848, [.20, .43, .45], 1.5);
        $content .= $this->text(self::LEFT, 823, 'SCHOLARSHIP APPLICATION REPORT', 15, 'F2', [.12, .24, .27]);
        $content .= $this->text(self::LEFT, 807, 'Consolidated administrative application records', 8, 'F1', [.43, .51, .53]);

        $metaY = 760;
        $metaHeight = 34;
        $metaWidths = [185, 185, 186];
        $meta = [
            ['REPORT DATE', (string) ($metadata['generated_at'] ?? '')],
            ['APPLICATION RECORDS', (string) ($metadata['count'] ?? '0')],
            ['STATUS', (string) ($metadata['status'] ?? 'All statuses')],
        ];
        $x = self::LEFT;
        foreach ($meta as $index => [$label, $value]) {
            $width = $metaWidths[$index];
            $content .= $this->rect($x, $metaY, $width, $metaHeight, [.97, .98, .98], [.82, .86, .87]);
            $content .= $this->text($x + 8, $metaY + 21, $label, 6, 'F2', [.48, .56, .58]);
            $content .= $this->text($x + 8, $metaY + 8, $this->truncate($value, 44), 8, 'F2', [.20, .31, .33]);
            $x += $width;
        }

        $filterText = [];
        if (($metadata['search'] ?? '') !== '') {
            $filterText[] = 'Search: ' . $metadata['search'];
        }
        if (($metadata['scholarship'] ?? '') !== '') {
            $filterText[] = 'Scholarship: ' . $metadata['scholarship'];
        }
        if ($filterText !== []) {
            $content .= $this->text(self::LEFT, 743, $this->truncate(implode('    |    ', $filterText), 110), 7, 'F1', [.37, .47, .49]);
            $tableTop = 720;
        } else {
            $tableTop = 735;
        }

        $content .= $this->text(self::LEFT, $tableTop + 11, 'APPLICATION RECORDS', 8, 'F2', [.19, .34, .37]);
        $headers = ['#', 'REF.', 'APPLICANT', 'SCHOLARSHIP', 'CONTACT DETAILS', 'DATE SUBMITTED', 'STATUS'];
        $x = self::LEFT;
        $headerY = $tableTop - 8;
        foreach ($headers as $index => $header) {
            $width = self::COLUMNS[$index];
            $content .= $this->rect($x, $headerY - 22, $width, 22, [.13, .45, .28], [.08, .36, .20]);
            $content .= $this->text($x + 4, $headerY - 14, $header, $index === 0 ? 6 : 6.5, 'F2', [1, 1, 1]);
            $x += $width;
        }

        return [$content, $headerY - 22];
    }

    private function finishPage(string $content, int $pageNumber): string
    {
        $content .= $this->line(self::LEFT, 53, self::LEFT + self::TABLE_WIDTH, 53, [.72, .78, .79]);
        $content .= $this->text(self::LEFT, 39, 'MUNICIPAL SCHOLARSHIP SYSTEM  |  ADMINISTRATIVE COPY', 7, 'F2', [.37, .47, .49]);
        $content .= $this->text(480, 39, 'PAGE ' . $pageNumber, 7, 'F2', [.37, .47, .49]);

        return $content;
    }

    private function wrap(string $value, int $width, int $fontSize): array
    {
        $value = $this->toPdfText($value);
        $maxChars = max(8, (int) floor($width / ($fontSize * .52)));
        $lines = [];
        foreach (explode("\n", $value) as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                $lines[] = '';
                continue;
            }
            $words = preg_split('/\s+/', $paragraph) ?: [];
            $line = '';
            foreach ($words as $word) {
                while (strlen($word) > $maxChars) {
                    if ($line !== '') {
                        $lines[] = $line;
                        $line = '';
                    }
                    $lines[] = substr($word, 0, $maxChars);
                    $word = substr($word, $maxChars);
                }
                $candidate = $line === '' ? $word : $line . ' ' . $word;
                if (strlen($candidate) > $maxChars && $line !== '') {
                    $lines[] = $line;
                    $line = $word;
                } else {
                    $line = $candidate;
                }
            }
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return $lines !== [] ? $lines : [''];
    }

    private function truncate(string $value, int $maxChars): string
    {
        $value = $this->toPdfText($value);
        return strlen($value) > $maxChars ? substr($value, 0, $maxChars - 3) . '...' : $value;
    }

    private function toPdfText(string $value): string
    {
        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                return $converted;
            }
        }

        return preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';
    }

    private function text(float $x, float $y, string $text, float $size, string $font, array $color): string
    {
        $cleanText = preg_replace('/[\x00-\x1F\x7F]/', ' ', $this->toPdfText($text)) ?? '';
        $encoded = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $cleanText);
        return sprintf(
            "q %.3f %.3f %.3f rg BT /%s %.2f Tf 1 0 0 1 %.2f %.2f Tm (%s) Tj ET Q\n",
            $color[0], $color[1], $color[2], $font, $size, $x, $y, $encoded
        );
    }

    private function line(float $x1, float $y1, float $x2, float $y2, array $color, float $width = .5): string
    {
        return sprintf(
            "q %.3f %.3f %.3f RG %.2f w %.2f %.2f m %.2f %.2f l S Q\n",
            $color[0], $color[1], $color[2], $width, $x1, $y1, $x2, $y2
        );
    }

    private function rect(float $x, float $y, float $width, float $height, array $fill, array $stroke): string
    {
        return sprintf(
            "q %.3f %.3f %.3f rg %.3f %.3f %.3f RG %.2f w %.2f %.2f %.2f %.2f re B Q\n",
            $fill[0], $fill[1], $fill[2], $stroke[0], $stroke[1], $stroke[2], .45, $x, $y, $width, $height
        );
    }

    private function loadPngLogo(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new \RuntimeException('The municipal logo could not be read for the application report PDF.');
        }
        $png = file_get_contents($path);
        if ($png === false || substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            throw new \RuntimeException('The municipal logo is not a valid PNG image.');
        }

        $offset = 8;
        $width = 0;
        $height = 0;
        $bitDepth = 0;
        $colorType = -1;
        $interlace = -1;
        $compressed = '';
        $length = strlen($png);
        while ($offset + 8 <= $length) {
            $chunkLength = unpack('Nlength', substr($png, $offset, 4))['length'];
            $chunkType = substr($png, $offset + 4, 4);
            $chunkData = substr($png, $offset + 8, $chunkLength);
            if ($chunkType === 'IHDR') {
                $header = unpack('Nwidth/Nheight/CbitDepth/CcolorType/Ccompression/Cfilter/Cinterlace', $chunkData);
                $width = (int) $header['width'];
                $height = (int) $header['height'];
                $bitDepth = (int) $header['bitDepth'];
                $colorType = (int) $header['colorType'];
                $interlace = (int) $header['interlace'];
            } elseif ($chunkType === 'IDAT') {
                $compressed .= $chunkData;
            } elseif ($chunkType === 'IEND') {
                break;
            }
            $offset += 12 + $chunkLength;
        }

        $channels = $colorType === 6 ? 4 : ($colorType === 2 ? 3 : 0);
        if ($width < 1 || $height < 1 || $bitDepth !== 8 || $channels === 0 || $interlace !== 0) {
            throw new \RuntimeException('The municipal logo has an unsupported PNG color format.');
        }

        $filtered = gzuncompress($compressed);
        if ($filtered === false) {
            throw new \RuntimeException('The municipal logo image data could not be decoded.');
        }
        $rowBytes = $width * $channels;
        $expectedLength = $height * ($rowBytes + 1);
        if (strlen($filtered) !== $expectedLength) {
            throw new \RuntimeException('The municipal logo image data is incomplete.');
        }

        $rgb = '';
        $alpha = '';
        $previous = str_repeat("\0", $rowBytes);
        $position = 0;
        for ($rowIndex = 0; $rowIndex < $height; $rowIndex++) {
            $filter = ord($filtered[$position]);
            $position++;
            $row = substr($filtered, $position, $rowBytes);
            $position += $rowBytes;
            $reconstructed = '';
            for ($i = 0; $i < $rowBytes; $i++) {
                $value = ord($row[$i]);
                $left = $i >= $channels ? ord($reconstructed[$i - $channels]) : 0;
                $above = ord($previous[$i]);
                $upperLeft = $i >= $channels ? ord($previous[$i - $channels]) : 0;
                $predictor = match ($filter) {
                    0 => 0,
                    1 => $left,
                    2 => $above,
                    3 => (int) floor(($left + $above) / 2),
                    4 => $this->paethPredictor($left, $above, $upperLeft),
                    default => throw new \RuntimeException('The municipal logo uses an unsupported PNG filter.'),
                };
                $reconstructed .= chr(($value + $predictor) & 0xff);
            }

            if ($channels === 4) {
                for ($pixel = 0; $pixel < $width; $pixel++) {
                    $pixelOffset = $pixel * 4;
                    $rgb .= substr($reconstructed, $pixelOffset, 3);
                    $alpha .= $reconstructed[$pixelOffset + 3];
                }
            } else {
                $rgb .= $reconstructed;
            }
            $previous = $reconstructed;
        }

        $rgbCompressed = gzcompress($rgb);
        $alphaCompressed = $channels === 4 ? gzcompress($alpha) : null;
        if ($rgbCompressed === false || ($channels === 4 && $alphaCompressed === false)) {
            throw new \RuntimeException('The municipal logo could not be prepared for PDF output.');
        }

        return [
            'width' => $width,
            'height' => $height,
            'rgb' => $rgbCompressed,
            'alpha' => $alphaCompressed,
        ];
    }

    private function paethPredictor(int $left, int $above, int $upperLeft): int
    {
        $estimate = $left + $above - $upperLeft;
        $leftDistance = abs($estimate - $left);
        $aboveDistance = abs($estimate - $above);
        $upperLeftDistance = abs($estimate - $upperLeft);

        if ($leftDistance <= $aboveDistance && $leftDistance <= $upperLeftDistance) return $left;
        if ($aboveDistance <= $upperLeftDistance) return $above;
        return $upperLeft;
    }

    private function buildPdf(array $pages, ?array $logo = null): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $kids = [];
        $nextId = 5;
        $xObjectResources = '';
        if ($logo !== null) {
            $logoId = $nextId++;
            $maskId = $logo['alpha'] !== null ? $nextId++ : null;
            $maskReference = $maskId !== null ? ' /SMask ' . $maskId . ' 0 R' : '';
            $objects[$logoId] = '<< /Type /XObject /Subtype /Image /Width ' . $logo['width']
                . ' /Height ' . $logo['height'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode'
                . $maskReference . ' /Length ' . strlen($logo['rgb']) . " >>\nstream\n" . $logo['rgb'] . "\nendstream";
            if ($maskId !== null) {
                $objects[$maskId] = '<< /Type /XObject /Subtype /Image /Width ' . $logo['width']
                    . ' /Height ' . $logo['height'] . ' /ColorSpace /DeviceGray /BitsPerComponent 8 /Filter /FlateDecode'
                    . ' /Length ' . strlen($logo['alpha']) . " >>\nstream\n" . $logo['alpha'] . "\nendstream";
            }
            $xObjectResources = ' /XObject << /Logo ' . $logoId . ' 0 R >>';
        }

        foreach ($pages as $pageContent) {
            $contentId = $nextId++;
            $pageId = $nextId++;
            $objects[$contentId] = "<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "endstream";
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 936] /Resources << /Font << /F1 3 0 R /F2 4 0 R >>' . $xObjectResources . ' >> /Contents ' . $contentId . ' 0 R >>';
            $kids[] = $pageId . ' 0 R';
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($pages) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xrefOffset = strlen($pdf);
        $objectCount = count($objects) + 1;
        $pdf .= "xref\n0 " . $objectCount . "\n0000000000 65535 f \n";
        for ($id = 1; $id < $objectCount; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
        }
        $pdf .= "trailer\n<< /Size " . $objectCount . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";

        return $pdf;
    }
}

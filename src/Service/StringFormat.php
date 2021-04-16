<?php
namespace App\Service;

class StringFormat
{
    public function getSeoUrl($inputString)
    {
        //$inputString = "Muster Immobilien Invest Firma GmbH & Co. KG in München";
        $patterns = array();
        $patterns[0] = '/GmbH & Co. KG/';
        $patterns[1] = '/GmbH & Co OHG/';
        $patterns[2] = '/VVaG./';
        $patterns[3] = '/GbR/';
        $patterns[4] = '/OHG/';
        $patterns[5] = '/KG/';
        $patterns[6] = '/Stille Gesellschaft/';
        $patterns[7] = '/PartG./';
        $patterns[8] = '/AG/';
        $patterns[9] = '/GmbH/';
        $patterns[10] = '/UG/';
        $patterns[11] = '/KGaA./';
        $patterns[12] = '/Stiftung/';
        $patterns[13] = '/Genossenschaft/';
        $patterns[14] = '/ä/';
        $patterns[15] = '/ö/';
        $patterns[16] = '/ß/';
        $patterns[17] = '/ü/';
        $patterns[18] = '/ in /';
        $patterns[19] = '/\./';

        $replacements = array();
        $replacements[0] = '';
        $replacements[1] = '';
        $replacements[2] = '';
        $replacements[3] = '';
        $replacements[4] = '';
        $replacements[5] = '';
        $replacements[6] = '';
        $replacements[7] = '';
        $replacements[8] = '';
        $replacements[9] = '';
        $replacements[10] = '';
        $replacements[11] = '';
        $replacements[12] = '';
        $replacements[13] = '';
        $replacements[14] = 'ae';
        $replacements[15] = 'oe';
        $replacements[16] = 'ss';
        $replacements[17] = 'ue';
        $replacements[18] = '';
        $replacements[19] = ' ';
        
        $result = preg_replace($patterns, $replacements, $inputString);
        $result = trim(preg_replace('/\s+/', ' ', $result));
        $result = preg_replace('/ /', '-', $result);
        $seoUrl = strtolower($result);

        return $seoUrl;
    }

}
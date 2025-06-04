<?php

namespace Webkul\Email\Helpers;

class HtmlFilter
{
    public function tln_tagprint($tagname, $attary, $tagtype)
    {
        if ($tagtype == 2) {
            return '</' . $tagname . '>';
        }

        $fulltag = '<' . $tagname;

        if (is_array($attary) && count($attary)) {
            $atts = [];

            foreach ($attary as $attname => $attvalue) {
                $atts[] = "$attname=$attvalue";
            }

            $fulltag .= ' ' . implode(' ', $atts);
        }

        if ($tagtype == 3) {
            $fulltag .= ' /';
        }

        return $fulltag . '>';
    }

    public function tln_casenormalize(&$val)
    {
        $val = strtolower($val);
    }

    public function tln_skipspace($body, $offset)
    {
        preg_match('/^(\s*)/s', substr($body, $offset), $matches);

        return $offset + (isset($matches[1]) ? strlen($matches[1]) : 0);
    }

    public function tln_findnxstr($body, $offset, $needle)
    {
        $pos = strpos($body, $needle, $offset);

        return $pos === false ? strlen($body) : $pos;
    }

    public function tln_findnxreg($body, $offset, $reg)
    {
        $preg_rule = '%^(.*?)(' . $reg . ')%s';
        preg_match($preg_rule, substr($body, $offset), $matches);

        if (empty($matches[0])) {
            return false;
        }

        return [
            $offset + strlen($matches[1]),
            $matches[1],
            $matches[2],
        ];
    }

    public function tln_deent(&$attvalue, $regex, $hex = false)
    {
        preg_match_all($regex, $attvalue, $matches);

        if (!empty($matches[0])) {
            $repl = [];

            foreach ($matches[0] as $i => $match) {
                $numval = $hex ? hexdec($matches[1][$i]) : $matches[1][$i];
                $repl[$match] = chr($numval);
            }

            $attvalue = strtr($attvalue, $repl);
            return true;
        }

        return false;
    }

    public function tln_defang(&$attvalue)
    {
        if (strpos($attvalue, '&') === false && strpos($attvalue, '\\') === false) {
            return;
        }

        do {
            $m  = $this->tln_deent($attvalue, '/\&#0*(\d+);*/s');
            $m |= $this->tln_deent($attvalue, '/\&#x0*((\d|[a-f])+);*/si', true);
            $m |= $this->tln_deent($attvalue, '/\\\\(\d+)/s', true);
        } while ($m);

        $attvalue = stripslashes($attvalue);
    }

    public function tln_unspace(&$attvalue)
    {
        if (strcspn($attvalue, "\t\r\n\0 ") != strlen($attvalue)) {
            $attvalue = str_replace(["\t", "\r", "\n", "\0", ' '], '', $attvalue);
        }
    }

    // Aqui você pode continuar implementando os demais métodos conforme sua necessidade,
    // como tln_getnxtag, tln_fixatts, tln_fixurl, tln_fixstyle, tln_sanitize, process, etc.
}

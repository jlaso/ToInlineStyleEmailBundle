<?php
/**
 * User: avasilenko
 * Date: 5/2/13
 * Time: 17:30
 */
namespace RobertoTru\ToInlineStyleEmailBundle\Twig;

use Twig_Compiler;

class InlineCssNode extends \Twig_Node 
{
    private $debug;
    private $parameters;
    
    public function __construct(\Twig_NodeInterface $body, $css, $parameters, $lineno = 0, $debug, $tag = 'inlinecss')
    {
        $this->debug = $debug;
        $this->parameters = $parameters;
        parent::__construct(array('body' => $body), array('css' => $css), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
            $cssFile = <<<EOF

        \$getCssLocale = function(\$base, \$locale){
                \$result = file_get_contents(\$base);
                \$css = explode(".css", \$base);
                \$css = \$css[0] . "-" . \$locale . ".css";
                //if(file_exists(\$css)){
                    \$result .= PHP_EOL . file_get_contents(\$css);
                //}

            return '"' . addslashes(\$result) . '"';
        };

EOF;

        $locale = "\$context[\"" . $this->parameters['locale']->getAttribute('name') . "\"]";
        $locale = "isset(" . $locale .") ? " . $locale . " : \"en\"";
        $css = sprintf("\$getCssLocale('%s', %s)", $this->getAttribute('css'), $locale);
        $compiler->addDebugInfo($this)
            ->write($cssFile)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write(sprintf('echo $context["inlinecss"]->inlineCSS(ob_get_clean(), %s);' . "\n", $css ));


//
//        if ($this->debug) {
//            $css = sprintf("file_get_contents('%s')", $this->getAttribute('css'));
//        } else {
//            $css = '"' . addslashes(file_get_contents($this->getAttribute('css'))) . '"';
//        }
//        $compiler->addDebugInfo($this)
//            ->write("ob_start();\n")
//            ->subcompile($this->getNode('body'))
//            ->write(sprintf('echo $context["inlinecss"]->inlineCSS(ob_get_clean(), %s);' . "\n", $css));


    }
}

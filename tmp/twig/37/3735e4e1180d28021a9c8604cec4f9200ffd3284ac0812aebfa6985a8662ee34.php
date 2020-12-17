<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* 404.twig */
class __TwigTemplate_e6132aeb0e2f633194cb08fe7e9c49d3fe665bbc68b804c03fe4dab606c4b08d extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return $this->loadTemplate((((0 !== twig_compare(($context["isAjaxRequest"] ?? null), true))) ? ("base.twig") : ("ajax.twig")), "404.twig", 1);
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "    ";
        echo twig_escape_filter($this->env, ($context["apptitle"] ?? null), "html", null, true);
        echo " - Page not found
";
    }

    // line 7
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 8
        echo "    <div class=\"container\">
        <div class=\"alert alert-secondary\" role=\"alert\">
            <h3 class=\"alert-heading\">";
        // line 10
        echo twig_escape_filter($this->env, ($context["apptitle"] ?? null), "html", null, true);
        echo "</h3><hr>
               
                <p style=\"font-size: 1.5rem;\" class=\"mt-3 text-center\">
                    Sorry, an error has occured,<br>
                    Requested page not found!
                </p>
                ";
        // line 16
        if (($context["ServerSignature"] ?? null)) {
            // line 17
            echo "                    <hr>
                    <p class=\"text-right mb-0\">";
            // line 18
            echo twig_escape_filter($this->env, ($context["ServerSignature"] ?? null), "html", null, true);
            echo "</p>
                ";
        }
        // line 20
        echo "            </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "404.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 20,  79 => 18,  76 => 17,  74 => 16,  65 => 10,  61 => 8,  57 => 7,  50 => 4,  46 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "404.twig", "E:\\dev\\php7\\slim\\app\\templates\\404.twig");
    }
}

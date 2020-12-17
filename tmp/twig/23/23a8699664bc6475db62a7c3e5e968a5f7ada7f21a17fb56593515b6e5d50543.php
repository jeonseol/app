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

/* base/header.twig */
class __TwigTemplate_20880659277d60b52e93403d20b0017ec7d8b6992cc960281dd4073b04e09e28 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<header class=\"navbar navbar-expand-md navbar-toggleable-sm navbar navbar-dark bg-blue fixed-top\">
    <nav class=\"container\">
        <a class=\"navbar-brand\" href=\"";
        // line 3
        echo twig_escape_filter($this->env, $this->env->getRuntime('Slim\Views\TwigRuntimeExtension')->urlFor("home"), "html", null, true);
        echo "\">
            <img class=\"align-middle\"  src=\"";
        // line 4
        echo twig_escape_filter($this->env, $this->env->getRuntime('Slim\Views\TwigRuntimeExtension')->getBasePath(), "html", null, true);
        echo "/assets/img/favicon.png\"   width=\"24\">
            <span class=\"align-middle ml-2\">";
        // line 5
        echo twig_escape_filter($this->env, ($context["apptitle"] ?? null), "html", null, true);
        echo "</span>
        </a>
        <button class=\"navbar-toggler collapsed\" type=\"button\" data-toggle=\"collapse\" data-target=\".main-menu\">
            <span class=\"navbar-toggler-icon\"></span>
        </button>
        <div class=\"navbar-collapse bg-blue collapse main-menu\">
         
        </div>
    </nav>
</header>
<div class=\"separator\"></div>";
    }

    public function getTemplateName()
    {
        return "base/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  49 => 5,  45 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base/header.twig", "E:\\dev\\php7\\slim\\app\\templates\\base\\header.twig");
    }
}

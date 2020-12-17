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

/* base.twig */
class __TwigTemplate_07f25c673a28d8afa399f145f0f21a3d0787e74d9ffb45d1a5140ee92d0ed568 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'meta' => [$this, 'block_meta'],
            'title' => [$this, 'block_title'],
            'styles' => [$this, 'block_styles'],
            'body' => [$this, 'block_body'],
            'scripts' => [$this, 'block_scripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <!-- Required meta tags -->
        <meta charset=\"utf-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
        <meta name=\"referrer\" content=\"no-referrer\">
        ";
        // line 8
        $this->displayBlock('meta', $context, $blocks);
        // line 9
        echo "
        <!-- Title -->
        <title>";
        // line 11
        $this->displayBlock('title', $context, $blocks);
        echo "</title>

        <!-- scripts -->
        <script src=\"https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js\"></script>
        
        <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js\" defer></script>
        <script src=\"https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js\" defer></script>

        <!--/ scripts -->
   
        <!-- Styles -->
        <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css\">
        <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css\">
        <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css\">
        <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.compat.min.css\">
        <link rel=\"stylesheet\" href=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getRuntime('Slim\Views\TwigRuntimeExtension')->getBasePath(), "html", null, true);
        echo "/assets/theme.css\">
        ";
        // line 27
        $this->displayBlock('styles', $context, $blocks);
        // line 28
        echo "        <!--/ Styles -->
        <link rel=\"icon\" href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getRuntime('Slim\Views\TwigRuntimeExtension')->getBasePath(), "html", null, true);
        echo "/assets/img/favicon.png\">
    </head>
    <body class=\"bg-light\">
        <div class=\"loader sticky top d-none\"></div>
        ";
        // line 33
        $this->loadTemplate("base/header.twig", "base.twig", 33)->display($context);
        // line 34
        echo "        ";
        $this->displayBlock('body', $context, $blocks);
        // line 35
        echo "        ";
        $this->displayBlock('scripts', $context, $blocks);
        // line 36
        echo "        ";
        $this->loadTemplate("base/footer.twig", "base.twig", 36)->display($context);
        // line 37
        echo "    </body>
</html>";
    }

    // line 8
    public function block_meta($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 11
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, ($context["apptitle"] ?? null), "html", null, true);
        if (($context["title"] ?? null)) {
            echo " - ";
            echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
        }
    }

    // line 27
    public function block_styles($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 34
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 35
    public function block_scripts($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 35,  130 => 34,  124 => 27,  113 => 11,  107 => 8,  102 => 37,  99 => 36,  96 => 35,  93 => 34,  91 => 33,  84 => 29,  81 => 28,  79 => 27,  75 => 26,  57 => 11,  53 => 9,  51 => 8,  42 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base.twig", "E:\\dev\\php7\\slim\\app\\templates\\base.twig");
    }
}

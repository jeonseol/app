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

/* base/footer.twig */
class __TwigTemplate_bdd52a77b3ee067dd73509bfb0f019be623e9e471cf1f11647b54105e481618d extends Template
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
        echo "<footer class=\"footer text-center\">
    <p class=\"container \">
        ";
        // line 3
        echo twig_escape_filter($this->env, ($context["apptitle"] ?? null), "html", null, true);
        echo "
        &copy; 2021 <a target=\"_blank\" href=\"http://github.com/ngsoft\">Aymeric Anger</a>
    </p>
</footer>";
    }

    public function getTemplateName()
    {
        return "base/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base/footer.twig", "E:\\dev\\php7\\slim\\app\\templates\\base\\footer.twig");
    }
}

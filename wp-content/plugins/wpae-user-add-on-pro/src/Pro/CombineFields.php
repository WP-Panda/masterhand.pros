<?php

namespace Pmue\Pro;


class CombineFields
{
    /**
     * @param $user
     * @param &$acfs
     * @param $implode_delimiter
     * @param $preview
     * @param $combineMultipleFieldsValue
     * @param $article
     * @param $element_name
     * @param $fieldSnipped
     */
    public function processCombineFields($user, &$acfs, $implode_delimiter, $preview, $combineMultipleFieldsValue, $article, $element_name, $fieldSnipped)
    {
        $combineMultipleFieldsValue = stripslashes($combineMultipleFieldsValue);
        $snippetParser = new \Wpae\App\Service\SnippetParser();
        $snippets = $snippetParser->parseSnippets($combineMultipleFieldsValue);
        $engine = new XmlExportEngine(XmlExportEngine::$exportOptions);
        $engine->init_available_data();
        $engine->init_additional_data();
        $snippets = $engine->get_fields_options($snippets);

        $articleData = self::prepare_data($user, $snippets, false, $acfs, $implode_delimiter, $preview);

        $functions = $snippetParser->parseFunctions($combineMultipleFieldsValue);
        $combineMultipleFieldsValue = \Wpae\App\Service\CombineFields::prepareMultipleFieldsValue($functions, $combineMultipleFieldsValue, $articleData);

        if ($preview) {
            $combineMultipleFieldsValue = trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($combineMultipleFieldsValue)));
        }

        wp_all_export_write_article($article, $element_name, pmxe_filter($combineMultipleFieldsValue, $fieldSnipped));

    }


}
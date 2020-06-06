<?php

class TauronPowerOutagesHandler
{
    public function menuTauronPowerOutages(array $hook_data = array())
    {
        $submenus = array(
            array(
                'name' => trans('Tauron power outages'),
                'link' => '?m=tauronpoweroutages',
                'tip' => trans('Tauron power outages'),
                'prio' => 150,
            ),
        );
        $hook_data['admin']['submenu'] = array_merge($hook_data['admin']['submenu'], $submenus);
        return $hook_data;
    }

    public function smartyTauronPowerOutages(Smarty $hook_data)
    {
        $template_dirs = $hook_data->getTemplateDir();
        $plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . LMSTauronPowerOutagesPlugin::PLUGIN_DIRECTORY_NAME . DIRECTORY_SEPARATOR . 'templates';
        array_unshift($template_dirs, $plugin_templates);
        $hook_data->setTemplateDir($template_dirs);
        return $hook_data;
    }

    public function modulesDirTauronPowerOutages(array $hook_data = array())
    {
        $plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . LMSTauronPowerOutagesPlugin::PLUGIN_DIRECTORY_NAME . DIRECTORY_SEPARATOR . 'modules';
        array_unshift($hook_data, $plugin_modules);
        return $hook_data;
    }

    public function welcomeTauronPowerOutages(array $hook_data = array())
    {
        // uncomment if you have current LMS version
        $SMARTY = LMSSmarty::getInstance();

        // uncomment if you have old LMS version
        //$SMARTY = $hook_data['smarty'];

        $commune = ConfigHelper::getConfig('tauron.commune');
        $commune = explode(",", (int) $commune);

        $district = ConfigHelper::getConfig('tauron.district');
        $district = explode(",", (int) $district);

        $api_url = ConfigHelper::getConfig('tauron.api_url', 'https://www.tauron-dystrybucja.pl/iapi');
        $array = array();

        $CURLConnection = curl_init();

        foreach ($commune as $gaid) {
            curl_setopt($CURLConnection, CURLOPT_URL, $api_url . "/outage/GetOutages?gaid=" . $gaid . "&type=commune");
            curl_setopt($CURLConnection, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($CURLConnection);
            $array = array_merge_recursive($array, json_decode($json, true));
        }

        foreach ($district as $gaid) {
            curl_setopt($CURLConnection, CURLOPT_URL, $api_url . "/outage/GetOutages?gaid=" . $gaid . "&type=district");
            curl_setopt($CURLConnection, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($CURLConnection);
            $json = curl_exec($CURLConnection);
            $array = array_merge_recursive($array, json_decode($json, true));
        }

        curl_close($CURLConnection);
        
        $SMARTY->assign('power_outages', $array);
        return $hook_data;
    }
}
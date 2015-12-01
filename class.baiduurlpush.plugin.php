<?php
/**
 * 'All Viewed' plugin for Vanilla Forums.
 *
 * @author Lincoln Russell <lincoln@vanillaforums.com>
 * @author Oliver Chung <shoat@cs.washington.edu>
 * @copyright 2009-2015 Vanilla Forums Inc.
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package AllViewed
 */

$PluginInfo['BaiduURLPush'] = array(
    'Name' => 'BaiduURLPush',
    'Description' => '链接自动实时推送到百度.',
    'Version' => '1.0',
    'SettingsUrl' => '/dashboard/settings/baiduurlpush',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => "xjtdy888",
    'AuthorEmail' => 'xjtdy888@gmail.com',
    'AuthorUrl' => 'http://www.sbbok.com',
    'License' => 'GNU GPLv2',
    'MobileFriendly' => true
);


class BaiduURLPushPlugin extends Gdn_Plugin {

    public function settingsController_baiduurlpush_create($Sender) {
        $Sender->permission('Garden.Settings.Manage');
        $Cf = new ConfigurationModule($Sender);

        $Formats = array_combine($this->Formats, $this->Formats);

        $Cf->initialize(array(
            'Plugins.BaiduURLPush.Site' => array('LabelCode' => '域 名', 'Control' => 'TextBox', 'Description' => '<p>请填写百度站长工具对应的域名</p>'),
            'Plugins.BaiduURLPush.Token' => array('LabelCode' => 'TOKEN', 'Control' => 'TextBox', 'Description' => '<p>请填写百度站长工具对应的TOKEN</p>'),
            'Plugins.BaiduURLPush.Disable' => array('LabelCode' => '禁用该功能', 'Control' => 'CheckBox', 'Description' => '<p>如果有问题,可以钩选该选项禁用此功能</p>')
        ));


        $Sender->addSideMenu();
        $Sender->setData('Title', '百度实时推送插件');
        $Cf->renderAll();

    }

    public function discussionModel_afterSaveDiscussion_handler($Sender) {
        $FormPostValues = val('FormPostValues', $Sender->EventArguments, array());
        $url = valr("Discussion.Url", $Sender->EventArguments);
        if (val('IsNewDiscussion', $FormPostValues, false) !== false) {
            $this->_push(array($url));
        }
    }

    protected function _push($urls) {
        $site  = c("Plugins.BaiduURLPush.Site", "");
        $token = c("Plugins.BaiduURLPush.Token", "");

        if (c("Plugins.BaiduURLPush.Disable", false)) {
            return ;
        }

        if (!$site || !$token) {
            return ;
        }
		return;

        $api = sprintf('http://data.zz.baidu.com/urls?site=%s&token=%s&type=original', $site, $token);
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }
}

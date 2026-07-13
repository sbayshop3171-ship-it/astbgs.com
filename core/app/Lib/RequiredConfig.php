<?php

namespace App\Lib;

class RequiredConfig{

    public function getConfig(){
        return [
            'general_setting' => [
                'title'=>'Configure basic setting of your site like Site Name, Currency, Timezone etc',
                'route'=>route('admin.setting.general')
            ],
            'logo_favicon' => [
                'title'=>'Update the logo and favicon',
                'route'=>route('admin.setting.logo.icon')
            ],
            'notification_template' => [
                'title'=>'Update the global notification template',
                'route'=>route('admin.setting.notification.global.email')
            ],
            'deposit_method' => [
                'title'=>'Setup at-least one payment method',
                'route'=>route('admin.gateway.automatic.index')
            ],
            'withdrawal_method' => [
                'title'=>'Setup at-least one withdrawal method',
                'route'=>route('admin.withdraw.method.create')
            ],
            'seo' => [
                'title'=>'Update the seo configuration',
                'route'=>route('admin.seo')
            ],
            'policy_content' => [
                'title'=>'Update the site policy content',
                'route'=>route('admin.frontend.sections','policy_pages')
            ],
            'membership_plan' => [
                'title'=>'Setup at-least one membership plan',
                'route'=>route('admin.plan.form')
            ],
            'product_category' => [
                'title'=>'Setup at-least one product category',
                'route'=>route('admin.category.index')
            ],
            'product_subcategory' => [
                'title'=>'Setup at-least one product subcategory',
                'route'=>route('admin.category.index')
            ]
        ];
    }

    public function totalConfigs(){
        return count($this->getConfig());
    }

    public function completedConfig(){
        return gs('config_progress') ?? [];
    }

    public function completedConfigCount(){
        return count($this->completedConfig() ?? []);
    }

    public function completedConfigPercent(){
        return ($this->completedConfigCount() / $this->totalConfigs()) * 100;
    }

    public static function configured($key){
        $completedConfig = gs('config_progress') ?? [];
        if (!in_array($key,$completedConfig)) {
            $general = gs();
            $general->config_progress = array_merge($completedConfig,[$key]);
            $general->save();
        }
    }
}
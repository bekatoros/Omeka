<?php

/**
 * @package Omeka\Db\Migration
 */
class setFrontPageOptions extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $themeName = Theme::getCurrentThemeName('public');
        $publicTheme = Theme::getTheme($themeName);
        $themePath = $publicTheme->getScriptPath();
        if(file_exists($themePath . '/custom.php')) {
            include($themePath . '/custom.php');    
        } else {
            //theme isn't updated to use front page options
            return;
        }
        
        if(function_exists('front_page_blocks')) {
            $newThemeOptions = front_page_blocks(array());    
        } else {
            //theme isn't updated to use front page options
            return;
        }
                
        $themePrimaryOptions = array();
        $themeSecondaryOptions = array();
        
        switch($themeName) {
            case 'default':
            case 'berlin':
                if(get_theme_option('Homepage Text')) {
                    $themePrimaryOptions['HomePageText'] = $newThemeOptions['HomePageText'];
                }

                if(get_theme_option('Display Featured Item')) {
                    $themePrimaryOptions['FeaturedItem'] = $newThemeOptions['FeaturedItem'];
                }                
                
                if(get_theme_option('Dislay Featured Collection')) {
                    $themePrimaryOptions['FeaturedCollection'] = $newThemeOptions['FeaturedCollection'];
                }

                if(get_theme_option('Display Featured Exhibit') && plugin_is_active('ExhibitBuilder')) {
                    $ebBlocks = exhibit_builder_front_page_blocks(array());
                    $themePrimaryOptions['ExhibitBuilderFeaturedExhibit'] = $ebBlocks['ExhibitBuilderFeaturedExhibit'];
                }     

                if(get_theme_option('Homepage Recent Items')) {
                    $themeSecondaryOptions['RecentItems'] = $newThemeOptions['RecentItems'];
                }
                
                
            break;

            case 'seasons':
                
            break;
        }
        set_option('front_page_primary', json_encode($themePrimaryOptions));
        set_option('front_page_secondary', json_encode($themeSecondaryOptions));
    }
}
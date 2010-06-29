<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Creates and populates the Omeka database schema.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
 **/
class Installer
{    
    
    const DEFAULT_USER_ACTIVE = true;
    const DEFAULT_USER_ROLE = 'super';
    
    const DEFAULT_PUBLIC_THEME = 'default';
    const DEFAULT_ADMIN_THEME = 'default';
    
    /**
     * @var Omeka_Db
     */
    private $_db;
    
    private $_formOptions = array(
        'administrator_email', 
        'copyright', 
        'site_title', 
        'author', 
        'description', 
        'thumbnail_constraint', 
        'square_thumbnail_constraint', 
        'fullsize_constraint', 
        'per_page_admin', 
        'per_page_public', 
        'show_empty_elements',
        'path_to_convert'
    );
    
    /**
     * Constructor.
     * 
     * @param Omeka_Db $db
     */
    public function __construct(Omeka_Db $db)
    {           
        $this->_db = $db;
    }
        
    /**
     * Install Omeka.
     * 
     * @param array $values Set of values required by the installer.  Usually
     * passed in via the form.
     * @param boolean $createUser Whether or not to create a new user along with
     * this installation.  Defaults to true.
     */
    public function install(array $values, $createUser = true)
    {
        if ($this->isInstalled()) {
            throw new RuntimeException("Omeka has already been installed.");
        }
        
        $db = $this->_db;
        
        $this->_createSchema();
        
        if ($createUser) {            
            $this->_createDefaultUser($values['username'], 
                                      $values['password'],
                                      $values['super_email']);
        }
        
        $this->_createMigrationTable();
        $this->_addOptions($values);
        return true;
    }
    
    /**
     * Determine whether Omeka has been installed.
     */
    public function isInstalled()
    {        
        $db = $this->_db;
        
        // Assume Omeka is not installed if the `options` table does not exist.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $db->fetchAll($sql);
        if (empty($tables)) {
            return false;
        }
        
        // Assume Omeka is not installed if the `options` table contains no rows.
        $optionCount = (int)$db->fetchOne("SELECT COUNT(id) FROM `{$db->prefix}options`");
        if (!$optionCount) {
            return false;
        }
        
        // Otherwise, assume Omeka is already installed.
        return true;
    }
    
    private function _createSchema()
    {
        $db = $this->_db;
        include INSTALL_DIR . DIRECTORY_SEPARATOR . 'install.sql.php';
        $this->_db->execBlock($installSql);
    }
    
    private function _createDefaultUser($username, $password, $email)
    {
        // Hack, prevents barfing when saving.
        Omeka_Context::getInstance()->setDb($this->_db);
        $user = new User;
        $user->Entity = new Entity;
        $user->Entity->email = $email;
        $user->Entity->first_name = Omeka_Form_Install::DEFAULT_USER_FIRST_NAME;
        $user->Entity->last_name = Omeka_Form_Install::DEFAULT_USER_LAST_NAME;
        $user->username = $username;
        $user->setPassword($password);
        $user->active = self::DEFAULT_USER_ACTIVE;
        $user->role = self::DEFAULT_USER_ROLE;
        $user->forceSave();
    }
    
    private function _createMigrationTable()
    {
        $migrations = Omeka_Db_Migration_Manager::getDefault($this->_db);
        $migrations->setupTimestampMigrations();
        $migrations->markAllAsMigrated();
    }
    
    private function _addOptions(array $values)
    {
        // Insert options.
        $optionSql = "
        INSERT INTO {$this->_db->Option} (
            name, 
            value
        ) VALUES (?, ?)";
        
        // Insert the form options to the options table.
        foreach ($this->_formOptions as $option) {
            $this->_db->exec($optionSql, array($option, $values[$option]));
        }
        
        // Insert default options to the options table. 
        $this->_db->exec($optionSql, array('admin_theme', self::DEFAULT_ADMIN_THEME));
        $this->_db->exec($optionSql, array('public_theme', self::DEFAULT_PUBLIC_THEME));
        $this->_db->exec($optionSql, array('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('disable_default_file_validation', 0));
                
        // If the fileinfo extension is not installed.
        $this->_db->exec($optionSql, array('enable_header_check_for_file_mime_types', (string)!extension_loaded('fileinfo')));
    }
}
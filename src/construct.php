<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * 
 * For constant initialization..
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

// path separate
defineCheck('MK3_PATH_SEPARATE', '/');

defineCheck('MK3_PATH_SEPARATE_NAMESPACE', '\\');

// config path
defineCheck('MK3_PATH_CONFIG', MK3_ROOT. MK3_PATH_SEPARATE . MK3_PATH_SEPARATE. 'config');

// app.php file path
defineCheck('MK3_PATH_APP', MK3_ROOT. MK3_PATH_SEPARATE . MK3_PATH_SEPARATE . 'app');

// path name of "rendering"
defineCheck('MK3_PATH_NAME_RENDERING', 'rendering');

// path name of "Rende" Class
defineCheck('MK3_PATH_NAME_RENDER', 'Render');

// path name of "View"
defineCheck('MK3_PATH_NAME_VIEW', 'View');

// path name of "Template"
defineCheck('MK3_PATH_NAME_TEMPLATE', 'Template');

// path name of "ViewPart"
defineCheck('MK3_PATH_NAME_VIEWPART', 'ViewPart');

// path rendering
defineCheck('MK3_PATH_RENDERING', MK3_ROOT. MK3_PATH_SEPARATE . MK3_PATH_SEPARATE .MK3_PATH_NAME_RENDERING);

// path render
defineCheck('MK3_PATH_RENDERING_RENDER', MK3_PATH_RENDERING . MK3_PATH_SEPARATE . MK3_PATH_NAME_RENDER);

// path view
defineCheck('MK3_PATH_RENDERING_VIEW', MK3_PATH_RENDERING . MK3_PATH_SEPARATE . MK3_PATH_NAME_VIEW);

defineCheck('MK3_PATH_RENDERING_TEMPLATE',MK3_PATH_RENDERING . MK3_PATH_SEPARATE . MK3_PATH_NAME_TEMPLATE);

defineCheck('MK3_PATH_RENDERING_VIEWPART',MK3_PATH_RENDERING . MK3_PATH_SEPARATE . MK3_PATH_NAME_VIEWPART);

defineCheck('MK3_DEFNS', 'app');

defineCheck('MK3_PATH_NAME_CONTROLLER', 'Controller');

defineCheck('MK3_PATH_NAME_PACK', 'Pack');

defineCheck('MK3_PATH_NAME_MODEL', 'Model');

defineCheck('MK3_PATH_NAME_RENDER', 'Render');

defineCheck('MK3_PATH_NAME_SHELL', 'Shell');

defineCheck('MK3_PATH_NAME_MIDDLEWARE', 'Middleware');

defineCheck('MK3_PATH_NAME_ELCLASS', 'ElClass');

defineCheck('MK3_DEFNS_CONTROLLER', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_CONTROLLER);

defineCheck('MK3_DEFNS_PACK', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_PACK);

defineCheck('MK3_DEFNS_MODEL', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_MODEL);

defineCheck('MK3_DEFNS_RENDER', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_RENDER);

defineCheck('MK3_DEFNS_SHELL', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_SHELL);

defineCheck('MK3_DEFNS_MIDDLEWARE', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_MIDDLEWARE);

defineCheck('MK3_DEFNS_ELCLASS', MK3_DEFNS . MK3_PATH_SEPARATE . MK3_PATH_NAME_ELCLASS);

defineCheck('MK3_PATH_NAME_TEMPORARIES', '.temporaries');

defineCheck('MK3_PATH_NAME_PUBLIC', '.public');

defineCheck('MK3_PATH_TEMPORARY', MK3_ROOT . MK3_PATH_SEPARATE . MK3_PATH_NAME_TEMPORARIES);

defineCheck('MK3_PATH_PUBLIC', MK3_ROOT . MK3_PATH_SEPARATE . MK3_PATH_NAME_PUBLIC);

defineCheck('MK3_CONTAINER', "container");

defineCheck('MK3_VIEW_EXTENSION',".view");

function defineCheck($name,$value){

	if(!defined($name)){
		define($name,$value);
	}

}
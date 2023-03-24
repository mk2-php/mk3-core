<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * 
 * For constant initialization..
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

// path separate
defineCheck('RLD_PATH_SEPARATE', '/');

defineCheck('RLD_PATH_SEPARATE_NAMESPACE', '\\');

defineCheck('RLD_PATH_NAME_CONFIG', 'config');

// config path
defineCheck('RLD_PATH_CONFIG', RLD_ROOT. RLD_PATH_SEPARATE . RLD_PATH_NAME_CONFIG);

// app.php file path
defineCheck('RLD_PATH_APP', RLD_ROOT. RLD_PATH_SEPARATE . 'app');

// path name of "rendering"
defineCheck('RLD_PATH_NAME_RENDERING', 'rendering');

// path name of "Rende" Class
defineCheck('RLD_PATH_NAME_RENDER', 'Render');

// path name of "View"
defineCheck('RLD_PATH_NAME_VIEW', 'View');

// path name of "Template"
defineCheck('RLD_PATH_NAME_TEMPLATE', 'Template');

// path name of "ViewPart"
defineCheck('RLD_PATH_NAME_VIEWPART', 'ViewPart');

// path rendering
defineCheck('RLD_PATH_RENDERING', RLD_ROOT. RLD_PATH_SEPARATE .RLD_PATH_NAME_RENDERING);

// path render
defineCheck('RLD_PATH_RENDERING_RENDER', RLD_PATH_RENDERING . RLD_PATH_SEPARATE . RLD_PATH_NAME_RENDER);

// path view
defineCheck('RLD_PATH_RENDERING_VIEW', RLD_PATH_RENDERING . RLD_PATH_SEPARATE . RLD_PATH_NAME_VIEW);

defineCheck('RLD_PATH_RENDERING_TEMPLATE',RLD_PATH_RENDERING . RLD_PATH_SEPARATE . RLD_PATH_NAME_TEMPLATE);

defineCheck('RLD_PATH_RENDERING_VIEWPART',RLD_PATH_RENDERING . RLD_PATH_SEPARATE . RLD_PATH_NAME_VIEWPART);

defineCheck('RLD_DEFNS', 'app');

defineCheck('RLD_PATH_NAME_CONTROLLER', 'Controller');

defineCheck('RLD_PATH_NAME_PACK', 'Pack');

defineCheck('RLD_PATH_NAME_MODEL', 'Model');

defineCheck('RLD_PATH_NAME_RENDER', 'Render');

defineCheck('RLD_PATH_NAME_SHELL', 'Shell');

defineCheck('RLD_PATH_NAME_MIDDLEWARE', 'Middleware');

defineCheck('RLD_PATH_NAME_EXCEPTION', 'Exception');

defineCheck('RLD_PATH_NAME_EXCEPTION_CLI', 'ExceptionCLI');

defineCheck('RLD_PATH_NAME_HOOK', 'Hook');

// defineCheck('RLD_DEFNS_CONTROLLER', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_CONTROLLER);

// defineCheck('RLD_DEFNS_PACK', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_PACK);

// defineCheck('RLD_DEFNS_MODEL', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_MODEL);

defineCheck('RLD_DEFNS_RENDER', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_RENDER);

// defineCheck('RLD_DEFNS_SHELL', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_SHELL);

// defineCheck('RLD_DEFNS_MIDDLEWARE', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_MIDDLEWARE);

// defineCheck('RLD_DEFNS_ELCLASS', RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_ELCLASS);

defineCheck('RLD_PATH_NAME_TEMPORARIES', '.temporaries');

defineCheck('RLD_PATH_NAME_PUBLIC', '.public');

defineCheck('RLD_PATH_TEMPORARY', RLD_ROOT . RLD_PATH_SEPARATE . RLD_PATH_NAME_TEMPORARIES);

defineCheck('RLD_PATH_PUBLIC', RLD_ROOT . RLD_PATH_SEPARATE . RLD_PATH_NAME_PUBLIC);

defineCheck('RLD_CONTAINER', "container");

defineCheck('RLD_VIEW_EXTENSION',".view");

function defineCheck($name,$value){

	if(!defined($name)){
		define($name,$value);
	}

}
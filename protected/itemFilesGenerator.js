/**
 * Created by jitheshgopan on 12/07/15.
 */


function toPascalCase(text) {
    return text.replace(/(\w)(\w*)/g, function(g0,g1,g2){
        return g1.toUpperCase() + g2.toLowerCase();
    });
}

function getRealFilePath(template, singular, plural, model, table) {
    var placeholders = {
        '{{singular}}' : singular,
        '{{plural}}' : plural,
        '{{singular-pascalCase}}': toPascalCase(singular),
        '{{plural-pascalCase}}': toPascalCase(plural),
        '{{model}}': model,
        '{{table}}': table
    };
    var regex;
    for(var i in placeholders) {
        reg = new RegExp(i, 'g');
        template = template.replace(reg, placeholders[i]);
    }
    return template;
}

function getTemplateFilePath(template) {
    return 'starterTemplates/' + getRealFilePath(template, 'item', 'items', 'Item', 'items');
}

function renderTemplate(templateContent, singular, plural, model, table) {
    var placeholders = {
        '@@singular@@' : singular,
        '@@plural@@' : plural,
        '@@singular-pascalCase@@': toPascalCase(singular),
        '@@plural-pascalCase@@': toPascalCase(plural),
        '@@model@@': model,
        '@@table@@': table
    };
    var regex;
    for(var i in placeholders) {
        reg = new RegExp(i, 'g');
        templateContent = templateContent.replace(reg, placeholders[i]);
    }
    return templateContent;
}

var Generators = {
    views: {
        folders: [
            'app/views/{{plural}}',
            'app/views/admin/{{plural}}'
        ],
        files: [
            /* Frontend views*/
            'app/views/home.blade.php',
            'app/views/header.blade.php',

            'app/views/{{plural}}/index.blade.php',
            'app/views/{{plural}}/{{plural}}List.blade.php',
            'app/views/{{plural}}/{{singular}}Item.blade.php',
            'app/views/{{plural}}/view{{singular-pascalCase}}.blade.php',
            /* Admin views*/
            'app/views/admin/{{plural}}/create.blade.php',
            'app/views/admin/{{plural}}/view.blade.php',
            'app/views/admin/index.blade.php',
            'app/views/admin/layout.blade.php',
            'app/views/admin/config/{{singular}}.blade.php',

            /* Errors */
            'app/views/errors/404.blade.php',
        ]
    },
    models: {
        folders: [
        ],
        files: [
            'app/models/{{model}}.php'
        ]
    },
    controllers: {
        folders: [

        ],
        files: [
            'app/controllers/{{singular-pascalCase}}Controller.php',
            'app/controllers/Admin{{plural-pascalCase}}Controller.php',
            'app/controllers/AdminConfigController.php',
        ]
    },
    helpers: {
        folders: [

        ],
        files: [
            'app/lib/helpers/{{singular-pascalCase}}Helpers.php'
        ]
    },
    schemas: {
        folders: [

        ],
        files: [
            'app/lib/Schemas/{{singular-pascalCase}}Config.json',
            'app/lib/Schemas/{{singular-pascalCase}}ConfigSchema.php',
            'app/lib/Schemas/Languages.json',
            'app/lib/Schemas/WidgetsData.json'
        ]
    },
    commands: {
        folders: [

        ],
        files: [
            'app/commands/DumpBasicDB.php'
        ]
    },

	others: {
		folders: [],
		files: [
            'app/routes.php',
			'app/filters.php',
            'public/css/index.less',
            'public/css/main.less',
            'public/css/view{{singular-pascalCase}}.less',
            'public/js/admin/{{singular}}Config.js'
		]
	}
}


function runGenerators(grunt, singular, plural, model, table) {
    var path;
    for(var i in Generators) {
        var len = Generators[i]['folders'].length;
        while(len--) {
            path = getRealFilePath(Generators[i]['folders'][len], singular, plural, model, table);
            grunt.file.mkdir(path);
        }

        len = Generators[i]['files'].length;
        while(len--) {
            var pathTemplate = Generators[i]['files'][len];
            path = getRealFilePath(pathTemplate, singular, plural, model, table);
            console.log(getTemplateFilePath(path));
            var templateContent = grunt.file.read(getTemplateFilePath(pathTemplate));
            grunt.file.write(path, renderTemplate(templateContent, singular, plural, model, table));
            grunt.log.write('Created file: ' + path);
        }
    }

}

module.exports = function(grunt) {
    return {
        run: function() {
            runGenerators(grunt, grunt.option('singular'), grunt.option('plural'), grunt.option('model'), grunt.option('table'));
            //grunt.file.mkdir(packageFolder);
        }
    }

}

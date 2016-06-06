<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 */

namespace console\controllers;

class MigrateController extends \yii\console\controllers\MigrateController
{

    /**
     * Pull migrations from git submodule repository
     */
    public function actionPull()
    {
        // TODO: pull and push
        $directory = __DIR__ . '/../migrations';
        exec(<<<CMD
cd $directory \
&& git checkout master \
&& git pull origin master
CMD
        );
    }

    /**
     * Push migration submodule to git remote repository
     */
    public function actionPush()
    {
        $directory = __DIR__ . '/../migrations';
//        $aa = exec("cd $directory && dir ", $a);
//        print_r($a);
        $cmds = [
            "cd $directory",
            'git add .',
            'git commit ',
        ];

        foreach ($cmds as $cmd) {
            echo "cmd> ", $cmd;
            exec($cmd, $output);
            echo $output;
        }
    }

    /**
     * Change method into public privacy
     * @inheritdoc
     */
    public function createMigrationHistoryTable()
    {
        parent::createMigrationHistoryTable();
    }

}
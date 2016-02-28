<?php

namespace Lembarek\Package\Package;

use Illuminate\Filesystem\Filesystem;

class Package implements PackageInterface
{

    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }


    /**
     * create a new package
     *
     * @param  string  $vendor
     * @param  string  $name
     * @return void
     */
    public function create($vendor, $name, $author_email, $author_name)
    {
        $path = getcwd().'/'.$vendor.'/'.$name;

        mkdir($path, 0777, true);

        $this->createComposer($vendor, $name, $author_email, $author_name, $path);

        $this->createSrc($path);

        $this->initGit($vendor, $name);

    }


    /**
     * create the composer.json file
     *
     * @param  string  $path
     * @return void
     */
    private function createComposer($vendor, $name, $author_email, $author_name, $path=__DIR__)
    {
        $composer  = $path.'/composer.json';
        $this->replaceAndSave(__DIR__.'/../templates/composer.json', '{{name}}', $name, $composer);
        $this->replaceAndSave($composer, '{{vendor}}', $vendor);
        $this->replaceAndSave($composer, '{{Vendor}}', ucfirst($vendor));
        $this->replaceAndSave($composer, '{{Name}}', ucfirst($name));
        $this->replaceAndSave($composer, '{{author_name}}', $author_name);
        $this->replaceAndSave($composer, '{{author_email}}', $author_email);
    }


    /**
     * create the src directory with its directories and files
     *
     * @param  string  $path
     * @return void
     */
    public function createSrc($path)
    {
        $src = $path.'/src';
        mkdir($src);
        exec('cp -R '.__DIR__."/../templates/src $path");
    }


     /**
     * Open haystack, find and replace needles, save haystack.
     *
     * @param  string $oldFile The haystack
     * @param  mixed  $search  String or array to look for (the needles)
     * @param  mixed  $replace What to replace the needles for?
     * @param  string $newFile Where to save, defaults to $oldFile
     *
     * @return void
     */
    public function replaceAndSave($oldFile, $search, $replace, $newFile = null)
    {
        $newFile = ($newFile == null) ? $oldFile : $newFile;
        $file = $this->fs->get($oldFile);
        $replacing = str_replace($search, $replace, $file);
        $this->fs->put($newFile, $replacing);
    }


    /**
     * init git
     *
     * @param string $vendor
     * @param string $name
     *
     * @return void
     */
    public function initGit($vendor, $name)
    {
        system("cd $vendor/$name && git init && git add . && git commit -m 'first init'");
    }

}

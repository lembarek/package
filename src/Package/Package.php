<?php

namespace Lembarek\Package\Package;

class Package implements PackageInterface
{

    protected $vendor;

    protected $name;

    protected $author_email;

    protected $author_name;

    protected $path;

    public function __construct($vendor='', $name='', $author_email='', $author_name='')
    {
        $this->setAll($vendor, $name, $author_email, $author_name);
    }


    /**
     * set all locals variables
     *
     * @param  string  $vendor
     * @param  string  $name
     * @param  string  $author_email
     * @param  string  $author_name
     * @return void
     */
    public function setAll($vendor, $name, $author_email, $author_name)
    {
        $this->vendor = $vendor;
        $this->name = $name;
        $this->author_email = $author_email;
        $this->author_name = $author_name;
        $this->path = getcwd().'/'.$this->vendor.'/'.$this->name;
        $this->composer = "$this->path/composer.json";

        return $this;
    }


    /**
     * create a new package
     *
     * @return void
     */
    public function create()
    {

        mkdir($this->path, 0777, true);

        $this->createSrc();

        $this->replaceAllInFiles(get_subdir_files($this->path));

        initGit($this->path);

    }


    /**
     * create the src directory with its directories and files
     *
     * @return void
     */
    private function createSrc()
    {
        $src = "$this->path/src";
        mkdir($src);
        exec('cp -R '.__DIR__."/../templates/* $this->path");
    }


    /**
     * replace variables with its values in all files
     *
     * @param  array  $files
     * @return void
     */
    public function replaceAllInFiles($files)
    {
        foreach($files as $file)
           $this->replaceAllInFile($file);
    }


    /**
     * replace variables with they values
     *
     * @param  string  $file
     * @return void
     */
    private function replaceAllInFile($file)
    {
        replaceAndSave($file, '{{name}}', $this->name);
        replaceAndSave($file, '{{vendor}}', $this->vendor);
        replaceAndSave($file, '{{Name}}', ucfirst($this->name));
        replaceAndSave($file, '{{Vendor}}', ucfirst($this->vendor));
        replaceAndSave($file, '{{author_name}}', $this->author_name);
        replaceAndSave($file, '{{author_email}}', $this->author_email);
    }
}

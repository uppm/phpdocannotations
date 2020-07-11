<?php
namespace modules\phpdocannotations;

use ReflectionClass;

class AnnotationParser {
    private $reflection;
    private $phpDoc;
    private $annotations;

    public function __construct($clazz, $useString = false){
        $this->annotations = [];
        if ($useString)
            $this->phpDoc = $clazz;
        else
            $this->phpDoc = (new ReflectionClass($clazz))->getDocComment();
        $this->parse();
    }

    public function parse(){
        foreach (explode("\n", $this->phpDoc) as $line) {
            $line = trim($line);
            if ($line != "/**" && $line != "*/") {
                $parsedLine = "";
                $splitLine = $line;
                if (isset($splitLine[0]) && $splitLine[0] == "*") {
                    $splitLine[0] = " ";
                }

                $parsedLine = trim($splitLine);
                
                if (isset($parsedLine[0]) && $parsedLine[0] == "@") {
                    $annotation = "";
                    $annotationClosed = false;
                    $parametersString = "";
                    foreach (str_split($parsedLine) as $char) {
                        if ($char == "(" && !$annotationClosed)
                            $annotationClosed = true;
                        
                        if ($char != "@" && $char != " " && !$annotationClosed){
                            $annotation .= $char;
                        }

                        if ($annotationClosed) {
                            $parametersString .= $char;
                        }
                    }

                    if (!$annotationClosed)
                        continue;

                    $parametersString[0] = " ";

                    $parametersString[strlen($parametersString)-1] = " ";

                    $className = trim($annotation);
                    if (!class_exists($className))
                        continue;
                    
                    $clazz = new ReflectionClass($className);
                    $instance = $clazz->newInstance();

                    foreach (explode(",", $parametersString) as $params) {
                        if (strpos($params, "=") !== false) {
                            $parts = explode("=", $params);

                            $parts[1] = trim($parts[1]);
                            if (isset($parts[1][0]) && isset($parts[1][strlen($parts[1])-1]) && $parts[1][0] == "\"" && $parts[1][strlen($parts[1])-1] == "\"") {
                                $parts[1] = substr_replace($parts[1], '', 0, 1);
                                $parts[1] = substr_replace($parts[1], '', strlen($parts[1])-1, 1);
                            }

                            if (is_numeric($parts[1]))
                                $parts[1] = (int) $parts[1];

                            $instance->{trim($parts[0])} = $parts[1];
                        }
                    }
                    $this->annotations[$className] = $instance;
                }

            }
        }

    }

    /**
     * Get the value of annotations
     */ 
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Get the a annotation
     */ 
    public function getAnnotation($clazz)
    {
        return $this->annotations[$clazz];
    }

    /**
     * Check if it has the annotation
     */ 
    public function hasAnnotation($clazz)
    {
        return isset($this->annotations[$clazz]);
    }

}
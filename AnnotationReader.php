<?php
/**
 * Created by PhpStorm.
 * User: erman.titiz
 * Date: 29/09/16
 * Time: 13:53
 */

namespace BiberLtd\Bundle\BaseEntityBundle;

use Doctrine\Common\Annotations\AnnotationReader AS AR;

class AnnotationReader extends AR
{
    /**
     * Get type of property from property declaration
     *
     * @param \ReflectionProperty $property
     *
     * @return null|string
     */
    public function getPropertyType(\ReflectionProperty $property)
    {
        $doc = $property->getDocComment();
        preg_match_all('#@(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            foreach ($annotations[1] as $annotation) {
                preg_match_all('#\s*(.*?)\s+#s', $annotation, $parts);
                if (!isset($parts[1])) {
                    continue;
                }
                $declaration = $parts[1];
                if (isset($declaration[0]) && $declaration[0] === 'var') {
                    if (isset($declaration[1])) {
                        if (substr($declaration[1], 0, 1) === '$') {
                            return null;
                        }
                        else {
                            return $declaration[1];
                        }
                    }
                }
            }
            return null;
        }
        return $doc;
    }
}
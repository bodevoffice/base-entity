<?php
/**
 * Created by PhpStorm.
 * User: erman.titiz
 * Date: 28/09/16
 * Time: 14:12
 */
namespace BiberLtd\Bundle\BaseEntityBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Util\Inflector;


class  BaseEntityRepository extends EntityRepository
{

    protected function build()
    {
        return $this->createQueryBuilder($this->getAlias());
    }
    protected function buildOne($id)
    {
        return $this->build()->where($this->getAlias().'.id = '.intval($id));
    }
    protected function buildAll()
    {
        return $this->build();
    }
    protected function getAlias()
    {
        $name = basename(str_replace('\\', '/', $this->getClassName()));
        return Inflector::tableize($name);
    }
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    public function __call($method, $arguments)
    {
        $newMethod = $method;
        $newArguments=$arguments;
        switch ($method) {
            case (preg_match('/findBy.*/', $method) ? true : false) :
                $newMethod='findBy';
                if(!is_array($arguments))
                {
                    $newArguments = array(
                        strtolower(str_replace('findBy','',$method)) => $arguments
                    );
                }
                break;
            case (preg_match('/findOneBy.*/', $method) ? true : false) :
                $newMethod='findOneBy';
                if(!is_array($arguments))
                {
                    $newArguments = array(
                        strtolower(str_replace('findOneBy','',$method)) => $arguments
                    );
                }
                break;
        }
        if (0 === strpos($method, 'find')) {
            if (method_exists($this, $builder = 'build'.substr($method, 4))) {
                $qb = call_user_func_array(array($this, $builder), $arguments);
                if (0 === strpos(substr($method, 4), 'One')) {
                    return $qb->getQuery()->getOneOrNullResult();
                }
                return $qb->getQuery()->getResult();
            }
        }
        return parent::__call($newMethod, $newArguments);
    }
}
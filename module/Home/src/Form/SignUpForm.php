<?php

namespace Home\Form;

use Home\Model\PositionRepositoryInterface;
use Laminas\Form\Form;

class SignUpForm extends Form
{
    /**
     * @var PositionRepositoryInterface
     */
    private $positionRepository;

    /**
     * @param PositionRepositoryInterface $positionRepository
     */
    public function __construct($positionRepository)
    {
        parent::__construct();
        $this->positionRepository = $positionRepository;
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }
}

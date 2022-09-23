<?php

namespace Application\Controller;

use Application\Form\Admin as Form;
use Application\Model\Command\PositionCommandInterface;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Entity\PositionList;
use Application\Model\Repository\PositionRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use InvalidArgumentException;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    /** @var string A sign of emptiness.
     * Indicates that there is no search for some column.
     */
    const EMPTY = '-';

    /**
     * @var Form\PositionForm
     */
    private Form\PositionForm $positionForm;
    /**
     * @var Form\UserForm
     */
    private Form\UserForm $userForm;
    /**
     * @var Form\AdminFilterForm
     */
    private Form\AdminFilterForm $adminFilterForm;
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;
    /**
     * @var PositionRepositoryInterface
     */
    private PositionRepositoryInterface $positionRepository;
    /**
     * @var UserCommandInterface
     */
    private UserCommandInterface $userCommand;
    /**
     * @var PositionCommandInterface
     */
    private PositionCommandInterface $positionCommand;

    /**
     * @param Form\PositionForm           $positionForm
     * @param Form\UserForm               $userForm
     * @param Form\AdminFilterForm        $adminFilterForm
     * @param UserRepositoryInterface     $userRepository
     * @param PositionRepositoryInterface $positionRepository
     * @param UserCommandInterface        $userCommand
     * @param PositionCommandInterface    $positionCommand
     */
    public function __construct(
        Form\PositionForm           $positionForm,
        Form\UserForm               $userForm,
        Form\AdminFilterForm        $adminFilterForm,
        UserRepositoryInterface     $userRepository,
        PositionRepositoryInterface $positionRepository,
        UserCommandInterface        $userCommand,
        PositionCommandInterface    $positionCommand
    ) {
        $this->positionForm = $positionForm;
        $this->userForm = $userForm;
        $this->adminFilterForm = $adminFilterForm;
        $this->userRepository = $userRepository;
        $this->positionRepository = $positionRepository;
        $this->userCommand = $userCommand;
        $this->positionCommand = $positionCommand;
    }

    public function viewUserListAction()
    {
        $viewModel = new ViewModel();

        $this->layout()->setVariable('headTitleName', 'Список пользователей (Администратор)');
        $this->layout()->setVariable('navbar', 'Laminas\Navigation\Admin');

        $viewModel->setVariables([
            'userInfo'           => $this->userRepository->findUsers(),
            'positionRepository' => $this->positionRepository,
            'page'               => 1,
            'adminFilterForm'    => $this->adminFilterForm,
        ]);

        return $viewModel;
    }

    public function getUsersAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            exit();
        }

        $data = $request->getPost()->toArray();
        parse_str($data['where'], $data['where']);
        $whereConfig = self::arrayFilterRecursive($data['where']);
        $orderConfig = $data['order'];
        $page = (integer)$data['page'];

        if (isset($data['updatePage'])) {
            $count = count($this->userRepository->findUsers($whereConfig));
            echo json_encode([
                'count'        => $count,
                'maxPageCount' => UserController::MAX_USER_COUNT,
            ]);
            exit();
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('partial/user-list-admin.phtml');

        $viewModel->setVariables([
            'userList' => $this->userRepository->findUsers(
                $whereConfig,
                $orderConfig,
                true,
                $page
            ),
            'formName' => $data['formName'],
        ]);

        return $viewModel;
    }

    public static function arrayFilterRecursive(array $array): array
    {
        foreach ($array as $key => & $value) {
            if (is_array($value)) {
                $value = AdminController::arrayFilterRecursive($value);
            }
            if (!$value) {
                unset($array[$key]);
            }
        }
        unset($value);

        return $array;
    }

    public function editUserAction()
    {
        $userId = (int)$this->params()->fromRoute('id', 0);

        if ($userId === 0) {
            return $this->redirect()->toRoute('admin/view-user-list');
        }

        try {
            $user = $this->userRepository->findUser($userId);
        } catch (InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('admin/view-user-list');
        }

        $this->layout()->setVariables([
            'headTitleName' => 'Редактирование пользователя (Администратор)',
            'navbar'        => 'Laminas\Navigation\Admin',
        ]);

        $this->userForm->bind($user);
        $viewModel = new ViewModel(['userForm' => $this->userForm]);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }

        $postData = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->userForm->setData($postData);

        if (!$this->userForm->isValid()) {
            return $viewModel;
        }

        $this->userCommand->updateUser($user);
        return $this->redirect()->toRoute('admin/view-user-list');
    }

    public function editPositionsAction()
    {
        $this->layout()->setVariables([
            'headTitleName' => 'Управление должностями (Администратор)',
            'navbar'        => 'Laminas\Navigation\Admin',
        ]);

        $list = $this->positionRepository->findAllPositions();
        $positionList = new PositionList($list);
        $this->positionForm->bind($positionList);

        $viewModel = new ViewModel(['positionForm' => $this->positionForm]);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }

        $this->positionForm->setData($request->getPost());

        if (!$this->positionForm->isValid()) {
            return $viewModel;
        }

        $this->positionCommand->updatePositions(
            $this->positionForm->getObject()
        );

        return $this->redirect()->toRoute('admin/edit-position');
    }
}

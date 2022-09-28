<?php

namespace Application\Controller;

use Application\Form\Messenger\DialogFilterForm;
use Application\Form\Messenger\NewMessageForm;
use Application\Helper\ConfigHelper;
use Application\Model\Command\MessageCommandInterface;
use Application\Model\Entity\Message;
use Application\Model\Repository\DialogRepositoryInterface;
use Application\Model\Repository\MessageRepositoryInterface;
use Application\Model\Repository\PositionRepositoryInterface;
use Application\Model\Repository\StatusRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container as SessionContainer;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use LogicException;
use RuntimeException;

class MessengerController extends AbstractActionController
{
    /** @var int Maximum number of uploaded messages at a time. */
    public const MAX_MESSAGE_COUNT = 20;

    private DialogFilterForm $dialogFilterForm;
    private NewMessageForm $newMessageForm;
    private DialogRepositoryInterface $dialogRepository;
    private UserRepositoryInterface $userRepository;
    private PositionRepositoryInterface $positionRepository;
    private MessageRepositoryInterface $messageRepository;
    private MessageCommandInterface $messageCommand;
    private SessionContainer $sessionContainer;
    private StatusRepositoryInterface $statusRepository;

    public function __construct(
        DialogFilterForm            $dialogFilterForm,
        NewMessageForm              $newMessageForm,
        DialogRepositoryInterface   $dialogRepository,
        UserRepositoryInterface     $userRepository,
        StatusRepositoryInterface   $statusRepository,
        PositionRepositoryInterface $positionRepository,
        MessageRepositoryInterface  $messageRepository,
        MessageCommandInterface     $messageCommand,
        SessionContainer            $sessionContainer
    ) {
        $this->dialogFilterForm = $dialogFilterForm;
        $this->newMessageForm = $newMessageForm;
        $this->dialogRepository = $dialogRepository;
        $this->userRepository = $userRepository;
        $this->statusRepository = $statusRepository;
        $this->positionRepository = $positionRepository;
        $this->messageRepository = $messageRepository;
        $this->messageCommand = $messageCommand;
        $this->sessionContainer = $sessionContainer;
    }

    public function viewDialogListAction()
    {
        $userId = $this->sessionContainer->offsetGet(LoginController::USER_ID_KEY);
        if (!is_integer($userId)) {
            return $this->redirect()->toRoute('home');
        }

        UserController::setAdminNavbar($this->statusRepository, $this, $userId);

        $this->layout()->setVariable('headTitleName', 'Dialogs');

        $dialogs = $this->dialogRepository->getDialogList($userId);

        return new ViewModel([
            'dialogs'            => $dialogs,
            'dialogFilterForm'   => $this->dialogFilterForm,
            'userRepository'     => $this->userRepository,
            'positionRepository' => $this->positionRepository,
        ]);
    }

    public function viewMessagesAction()
    {
        UserController::setAdminNavbar($this->statusRepository, $this, UserController::USER_ID);
        $buddyId = (int)$this->params()->fromRoute('id', 0);

        if ($buddyId === 0
            || $buddyId === UserController::USER_ID
        ) {
            return $this->redirect()->toRoute('user/view-dialog-list');
        }

        $viewModel = new ViewModel();

        $this->layout()->setVariable('headTitleName', 'Messages');

        $userInfo = $this->userRepository->findUser(UserController::USER_ID);
        $buddyInfo = $this->userRepository->findUser($buddyId);

        $viewModel->setVariables([
            'newMessageForm' => $this->newMessageForm,
            'userInfo'       => $userInfo,
            'buddyInfo'      => $buddyInfo,
        ]);

        return $viewModel;
    }

    public function getDialogsAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            throw new LogicException('The request to the address must be ajax and post.');
        }

        $data = ConfigHelper::filterEmpty($request->getPost()->toArray());

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('partial/dialog-list.phtml');

        $viewModel->setVariables([
            'dialogList'     => $this->dialogRepository->getDialogList(
                UserController::USER_ID,
                $data
            ),
            'userRepository' => $this->userRepository,
        ]);

        return $viewModel;

    }

    public function sendMessageAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            throw new LogicException('The request to the address must be ajax and post.');
        }

        $post = $request->getPost();
        $content = (string)$post->get('content');
        $buddyId = (int)$post->get('buddyId');

        try {
            $this->messageCommand->sendMessage(
                new Message(
                    $this->dialogRepository->getDialogId(UserController::USER_ID, $buddyId),
                    UserController::USER_ID,
                    date('Y-m-d H:i:s'),
                    $content,
                )
            );

            return new JsonModel(['ok']);
        } catch (RuntimeException $ex) {
            return new JsonModel([$ex->getMessage()]);
        }
    }

    public function loadMessagesAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            throw new LogicException('The request to the address must be ajax and post.');
        }

        $post = $request->getPost();
        $lastMessageId = $post->get('lastMessageId');
        $buddyId = (int)$post->get('buddyId');

        $messageList = $this->messageRepository->findMessagesOfDialog(
            $this->dialogRepository->getDialogId(UserController::USER_ID, $buddyId),
            $lastMessageId,
        );

        $messageList = $this->messageCommand->readBy(UserController::USER_ID, $messageList);

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('partial/message-list.phtml');
        $viewModel->setVariables([
            'messageList'    => $messageList,
            'userRepository' => $this->userRepository,
        ]);

        return $viewModel;
    }
}

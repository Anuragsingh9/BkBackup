<?php


namespace Modules\UserManagement\Repositories;


class BaseRepository {

    public IUserRepository $userRepository;
    public IEntityRepository $entityRepository;

    public function __construct(
        IUserRepository $userRepository,
        IEntityRepository $entityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->entityRepository = $entityRepository;
    }
}

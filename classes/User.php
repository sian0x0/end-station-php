<?php

class User
{
    private int $id;
    private string $userName;
    private string $role;
    private string $profilePicture;
    private string $password;
    private string $joinedDate;
    private static int $counter = 1;
    static array $usersStaticArr = [];

    /**
     * @param int $id
     * @param string $userName
     * @param string $role
     * @param string $profilePicture
     * @param string $password
     * @param string $joinedDate
     */
    public function __construct(string $userName, string $role, string $profilePicture, string $password, string $joinedDate, $id = null)
    {
        $this->id = $id ?? self::$counter++;
        $this->userName = $userName;
        $this->role = $role;
        $this->profilePicture = $profilePicture;
        $this->password = $password;
        $this->joinedDate = $joinedDate;
        self::$usersStaticArr[] = $this;
    }

    public static function generateSampleUsers(): array
    {
        return [
            new User(
                userName: 'admin',
                role: 1,
                profilePicture: '/public/img/assets/0.jpg',
                password: 'hashed_password_123',
                joinedDate: '2023-05-15',
                id: 1
            ),
            new User(
                userName: 'lou',
                role: 2,
                profilePicture: 'public/assets/img/profile/1.jpg',
                password: 'hashed_password_456',
                joinedDate: '2023-07-22',
                id: 2
            ),
            new User(
                userName: 'vullnet',
                role: 2,
                profilePicture: '/public/img/assets/2.jpg',
                password: 'hashed_password_789',
                joinedDate: '2023-09-10',
                id: 3
            )
        ];
    }

// getters and setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    private static function readUsersJSON()
    {
        if (file_exists('data/cache/users.json'))
        {
            $usersAssocArray = json_decode(file_get_contents('data/cache/users.json'), true);
            //clear central list of objects first
            self::$usersStaticArr = [];
            foreach ($usersAssocArray as $user) {
                $u = new User(
                    $user['id'],
                    $user['name'],
                    $user['role'],
                    $user['profilePicture'],
                    $user['password'],
                    $user['joinedDate'],
                );
                //$u->setUserVisits(); // #TODO: decide whether to implement here or just via DB
            }
        }
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getJoinedDate(): string
    {
        return $this->joinedDate;
    }

    public function setJoinedDate(string $joinedDate): void
    {
        $this->joinedDate = $joinedDate;
    }

    public static function getCounter(): int
    {
        return self::$counter;
    }

    public static function setCounter(int $counter): void
    {
        self::$counter = $counter;
    }

    public static function getUsersStaticArr(): array
    {
        return self::$usersStaticArr;
    }

    public static function setUsersStaticArr(array $usersStaticArr): void
    {
        self::$usersStaticArr = $usersStaticArr;
    }

// other getters with arguments
    public static function getUserNameById($id): ?string
    {
        foreach (User::$usersStaticArr as $user) {
            if ($id == $user->getId()) {
                return $user->getUsername();
            }
        }
        return null;
    }

    public static function loadData(): void
    {
        self::generateSampleUsers();
        self::readUsersJSON(); // #TODO: replace with PDO
    }

}
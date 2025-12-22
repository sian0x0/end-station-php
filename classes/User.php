<?php

class User
{
    private ?int $user_id = null; //null(able) initially, to be handled by the db
    private string $username;
    private string $role;
    private string $profile_picture;
    private string $password;
    private string $join_date;
    //private static int $counter = 1;


    public function __construct(
        string $username = "", //empty/null values needed for PDO::FETCH_CLASS
        string $role = "",
        string $profile_picture = "",
        string $password = "",
        string $join_date = "",
        ?int $user_id = null
    ) {
        $this->user_id = $user_id;
        $this->username = $username;
        $this->role = $role;
        $this->profile_picture = $profile_picture;
        $this->password = $password;
        $this->join_date = $join_date;
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
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    private static function readUsersJSON(): void
    {
        if (file_exists('data/cache/users.json'))
        {
            $usersAssocArray = json_decode(file_get_contents('data/cache/users.json'), true);
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

    public static function makeSelectOption(array $objectArray)
    {
        $entity = strtolower(get_class($objectArray[0]));
        $htmlString = '<select name="' . $entity . 'Id"' . 'id="' . $entity . '" multiple>';
        foreach ($objectArray as $object) { // display the name but submit the ID value
            $htmlString .= '<option value="' . $object->getId() . '">' . $object->getUserName() . '</option>';
        }
        return $htmlString . '</select><br>';
    }

    public static function getAll(): array
    {
        $db = DatabaseMain::getConnection();
        $sql = "SELECT user_id, username, join_date, profile_picture, role FROM users";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
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
        return $this->profile_picture;
    }

    public function setProfilePicture(string $profile_picture): void
    {
        $this->profile_picture = $profile_picture;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getJoindate(): string
    {
        return $this->join_date;
    }

    public function setJoindate(string $join_date): void
    {
        $this->join_date = $join_date;
    }

    public static function getCounter(): int
    {
        return self::$counter;
    }

    public static function setCounter(int $counter): void
    {
        self::$counter = $counter;
    }


// other getters with arguments
    public static function getUserNameById($user_id): ?string
    {
        $conn = DatabaseMain::getConnection();
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['username'];
    }

    public static function loadData(): void
    {
        self::generateSampleUsers();
        self::readUsersJSON(); // #TODO: replace with PDO
    }

}
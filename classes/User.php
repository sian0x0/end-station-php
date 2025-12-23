<?php

class User
{
    private ?int $user_id = null; //null(able) initially, to be handled by the db
    private ?string $username = null;
    private ?string $role = null;
    private ?string $profile_picture = null;
    private ?string $password = null;
    private ?string $join_date = null;

    public function __construct( //empty/null default values needed so that PDO::FETCH_CLASS can call the constructor with no args
        ?string $username = null,
        ?string $role = null,
        ?string $profile_picture = null,
        ?string $password = null,
        ?string $join_date = null,
        ?int $user_id = null
    ) {
        // The ??= operator ensures PDO data isn't overwritten by the constructor's default null arguments
        $this->user_id ??= $user_id;
        $this->username ??= $username;
        $this->role ??= $role;
        $this->profile_picture ??= $profile_picture;
        $this->password ??= $password;
        $this->join_date ??= $join_date;
    }

//    public static function generateSampleUsers(): array
//    {
//        return [
//            new User(
//                username: 'admin',
//                role: '1',
//                profile_picture: '/public/img/assets/0.jpg',
//                password: 'hashed_password_123',
//                join_date: '2023-05-15',
//                user_id: 1
//            ),
//            new User(
//                username: 'lou',
//                role: '2',
//                profile_picture: 'public/assets/img/profile/1.jpg',
//                password: 'hashed_password_456',
//                join_date: '2023-07-22',
//                user_id: 2
//            ),
//            new User(
//                username: 'vullnet',
//                role: '2',
//                profile_picture: '/public/img/assets/2.jpg',
//                password: 'hashed_password_789',
//                join_date: '2023-09-10',
//                user_id: 3
//            )
//        ];
//    }

// getters and setters
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getUsername(): string
    {
        return $this->username ?? "";
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Updated to use proper property names: user_id and username
     */
    public static function makeSelectOption(
        array $users,
        string $name = "guest_ids[]",
        array $selectedIds = [],
        ?int $excludeId = null
    ): string {
        if (empty($users)) return "";

        $htmlString = '<select name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" multiple style="height: 100px;">';

        foreach ($users as $user) {
            $id = $user->getUserId();

            // skip the logged in user if an id is provided
            if ($excludeId !== null && $id === $excludeId) continue;

            $selected = in_array($id, $selectedIds) ? " selected" : "";

            $htmlString .= '<option value="' . $id . '"' . $selected . '>';
            $htmlString .= htmlspecialchars($user->getUsername());
            $htmlString .= '</option>';
        }

        $htmlString .= '</select>';
        return $htmlString;
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
        return $this->role ?? "";
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getProfilePicture(): string
    {
        return $this->profile_picture ?? "";
    }

    public function setProfilePicture(string $profile_picture): void
    {
        $this->profile_picture = $profile_picture;
    }

    public function getPassword(): string
    {
        return $this->password ?? "";
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getJoindate(): string
    {
        return $this->join_date ?? "";
    }

    public function setJoindate(string $join_date): void
    {
        $this->join_date = $join_date;
    }

// other getters with arguments
    public static function getUserById(int $user_id): ?self
    {
        $conn = DatabaseMain::getConnection();
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchObject(self::class) ?: null;
    }

//html generators
    public static function generateTableHtml(): string
    {
        $users = self::getAll();
        $html = "<table class='users-table'>";
        $html .= "<thead><tr><th>id</th><th>username</th><th>joined date</th><th>role</th><th>last visit</th></tr></thead>";
        $html .= "<tbody>";

        foreach ($users as $user) {
            $user_id = $user->getUserId();

            // resolve last visit details
            $lastVisit = Visit::getLastVisitByUserId($user_id);
            $lastVisitDisplay = "none";

            if ($lastVisit) {
                $station = Station::getStationById($lastVisit->getStationId());
                $stationName = $station ? $station->getStationName() : "unknown";
                $date = date('j M Y', strtotime($lastVisit->getVisitDatetime()));
                $lastVisitDisplay = htmlspecialchars($stationName) . " on " . $date;
            }

            $html .= "<tr>";
            $html .= "<td>" . $user_id . "</td>";
            $html .= "<td><strong><a href='index.php?view=showUser&user_id=$user_id'>" . htmlspecialchars($user->getUsername()) . "</a></strong></td>";
            $html .= "<td>" . date('j M Y', strtotime($user->getJoindate())) . "</td>";
            $html .= "<td>" . htmlspecialchars($user->getRole()) . "</td>";
            $html .= "<td>" . $lastVisitDisplay . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }
}
<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $parentId = $_POST['parentId'] ?? '';
    $photo = $_FILES['photo'] ?? null;

    if ($name && $gender) {
        $data = [
            'name' => $name,
            'gender' => $gender,
            'parentId' => $parentId === 'none' ? null : $parentId,
            'photoUrl' => null,
        ];

        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            $bucket = $storage->getBucket();
            $object = $bucket->upload(
                file_get_contents($photo['tmp_name']),
                ['name' => 'family-photos/' . uniqid() . '-' . $photo['name']]
            );
            $data['photoUrl'] = $object->signedUrl(new \DateTime('tomorrow'));
        }

        $db->collection('family_members')->add($data);
    }
}

$familyMembers = $db->collection('family_members')->documents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree - Family Tree Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-org-chart@2.6.0"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8 text-center">Your Family Tree</h1>
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-1/3">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <h2 class="block text-gray-700 text-xl font-bold mb-2">Add Family Member</h2>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Name" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">
                                Gender
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="parentId">
                                Parent
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="parentId" name="parentId">
                                <option value="none">None</option>
                                <?php foreach ($familyMembers as $member): ?>
                                    <option value="<?php echo $member->id(); ?>"><?php echo $member['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="photo">
                                Photo
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="photo" name="photo" type="file" accept="image/*">
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Add Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="w-full md:w-2/3">
                <div id="family-tree" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
    <script>
        const familyMembers = <?php echo json_encode($familyMembers->rows()); ?>;
        
        const chart = new d3.OrgChart()
            .container('#family-tree')
            .data(familyMembers)
            .nodeWidth((d) => 120)
            .nodeHeight((d) => 120)
            .childrenMargin((d) => 40)
            .compact
MarginBetween((d) => 15)
            .compactMarginPair((d) => 80)
            .nodeContent((d) => {
                return `
                    <div style="padding: 5px; text-align: center;">
                        <img src="${d.data.photoUrl || '/placeholder.svg?height=80&width=80'}" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;" />
                        <div style="font-weight: bold; margin-top: 5px;">${d.data.name}</div>
                        <div>${d.data.gender}</div>
                    </div>
                `;
            });

        chart.render();
    </script>
</body>
</html>


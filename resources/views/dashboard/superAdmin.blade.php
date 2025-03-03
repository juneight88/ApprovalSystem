<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 2rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Super Admin Dashboard</h1>
            <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <button class="btn btn-primary mb-3" onclick="openAddUserModal()">Add User</button>
        
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>Subject Handled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr id="row-{{ $user->id }}">
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->department }}</td>
                    <td>{{ $user->program }}</td>
                    <td>{{ $user->department === 'BASIC EDUCATION' ? ($user->subject_handled ? json_decode($user->subject_handled)[0] : '-') : '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openEditModal({{ $user->id }})">Edit</button>
                        <form action="{{ route('user.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Add New User</h2>
                    <button type="button" class="btn-close" onclick="closeAddUserModal()"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store') }}" method="POST" id="addUserForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department:</label>
                            <select id="add-department" name="department" class="form-select" onchange="updatePrograms('add-department', 'add-program')" required>
                                <option value="">Select Department</option>
                                <option value="CCIS">CCIS</option>
                                <option value="CTE">CTE</option>
                                <option value="CBM">CBM</option>
                                <option value="CTHM">CTHM</option>
                                <option value="CAS">CAS</option>
                                <option value="CCJE">CCJE</option>
                                <option value="BASIC EDUCATION">BASIC EDUCATION</option>
                                <option value="OSAS">OSAS</option>
                                <option value="REGISTRAR">REGISTRAR</option>
                                <option value="EDP">EDP</option>
                                <option value="FINANCE">FINANCE</option>
                                <option value="HR">HR</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program:</label>
                            <select id="add-program" name="program" class="form-select">
                                <option value="">Select Program</option>
                            </select>
                        </div>
                        <div id="subject-handled-div" class="mb-3" style="display: none;">
                            <label class="form-label">Subject Handled:</label>
                            <select id="add-subject-handled" name="subject_handled" class="form-select">
                                <option value="">Select Subject</option>
                                <option value="ENGLISH">ENGLISH</option>
                                <option value="MATHEMATICS">MATHEMATICS</option>
                                <option value="SCIENCE">SCIENCE</option>
                                <option value="MAPEH">MAPEH</option>
                                <option value="FILIPINO">FILIPINO</option>
                                <option value="AP">AP</option>
                                <option value="TLE">TLE</option>
                                <option value="ICT">ICT</option>
                                <option value="VALUES EDUCATION">VALUES EDUCATION</option>
                                <option value="RESEARCH">RESEARCH</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role:</label>
                            <select name="role" class="form-select" required onchange="handleRoleChange(this, 'add-program')">
                                <option value="">Select Role</option>
                                <option value="Personnel">Personnel</option>
                                <option value="Non-teaching personnel">Non-teaching personnel</option>
                                <option value="Admin">Admin</option>
                                <option value="Head of Office">Head of Office</option>
                                <option value="Subject Coordinator">Subject Coordinator</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit User</h2>
                    <button type="button" class="btn-close" onclick="closeEditModal()"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" name="username" id="edit-username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="text" name="password" id="edit-password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department:</label>
                            <select id="edit-department" name="department" class="form-select" onchange="updatePrograms('edit-department', 'edit-program')" required>
                                <option value="">Select Department</option>
                                <option value="CCIS">CCIS</option>
                                <option value="CTE">CTE</option>
                                <option value="CBM">CBM</option>
                                <option value="CTHM">CTHM</option>
                                <option value="CAS">CAS</option>
                                <option value="CCJE">CCJE</option>
                                <option value="BASIC EDUCATION">BASIC EDUCATION</option>
                                <option value="OSAS">OSAS</option>
                                <option value="REGISTRAR">REGISTRAR</option>
                                <option value="EDP">EDP</option>
                                <option value="FINANCE">FINANCE</option>
                                <option value="HR">HR</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program:</label>
                            <select id="edit-program" name="program" class="form-select">
                                <option value="">Select Program</option>
                            </select>
                        </div>
                        <div id="edit-subject-handled-div" class="mb-3" style="display: none;">
                            <label class="form-label">Subject Handled:</label>
                            <select id="edit-subject-handled" name="subject_handled" class="form-select">
                                <option value="">Select Subject</option>
                                <option value="ENGLISH">ENGLISH</option>
                                <option value="MATHEMATICS">MATHEMATICS</option>
                                <option value="SCIENCE">SCIENCE</option>
                                <option value="MAPEH">MAPEH</option>
                                <option value="FILIPINO">FILIPINO</option>
                                <option value="AP">AP</option>
                                <option value="TLE">TLE</option>
                                <option value="ICT">ICT</option>
                                <option value="VALUES EDUCATION">VALUES EDUCATION</option>
                                <option value="RESEARCH">RESEARCH</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role:</label>
                            <select name="role" id="edit-role" class="form-select" required onchange="handleRoleChange(this, 'edit-program')">
                                <option value="">Select Role</option>
                                <option value="Personnel">Personnel</option>
                                <option value="Non-teaching personnel">Non-teaching personnel</option>
                                <option value="Admin">Admin</option>
                                <option value="Head of Office">Head of Office</option>
                                <option value="Subject Coordinator">Subject Coordinator</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const programs = {
            "CCIS": ["BSIT", "BSCS", "BSIS", "BLIS"],
            "CTE": ["BPED", "BSED-SS", "BSED-SCIENCE", "BSED-MATHEMATICS", "BTVTED", "BSED-ENGLISH"],
            "CBM": ["BSBA", "BPA/BSE", "BSAIS"],
            "CAS": ["AB"],
            "CCJE": ["BSCrim"],
            "CTHM": ["BSTM", "BSHM", "SCS"],
            "BASIC EDUCATION": ["ELEMENTARY", "JHS", "SHS"]
        };

        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
            document.getElementById('addUserForm').reset();
        }

        function openEditModal(userId) {
            fetch(`/user/edit/${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('editUserForm').action = `/user/edit/${userId}`;
                    document.getElementById('edit-username').value = user.username;
                    document.getElementById('edit-password').value = user.password;
                    document.getElementById('edit-department').value = user.department;
                    document.getElementById('edit-role').value = user.role;
                    
                    // Update programs dropdown
                    updatePrograms('edit-department', 'edit-program');
                    
                    // Set program value after programs are populated
                    setTimeout(() => {
                        document.getElementById('edit-program').value = user.program;
                    }, 100);
                    
                    // Handle subject handled for BASIC EDUCATION
                    const subjectHandledDiv = document.getElementById('edit-subject-handled-div');
                    if (user.department === 'BASIC EDUCATION') {
                        subjectHandledDiv.style.display = 'block';
                        if (user.subject_handled) {
                            try {
                                const subjectHandled = JSON.parse(user.subject_handled);
                                document.getElementById('edit-subject-handled').value = subjectHandled[0];
                            } catch (e) {
                                console.error('Error parsing subject_handled:', e);
                            }
                        }
                    } else {
                        subjectHandledDiv.style.display = 'none';
                    }
                    
                    document.getElementById('editUserModal').style.display = 'block';
                });
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
            document.getElementById('editUserForm').reset();
        }

        function updatePrograms(departmentId, programId) {
            const departmentSelect = document.getElementById(departmentId);
            const programSelect = document.getElementById(programId);
            const subjectHandledDiv = document.getElementById(departmentId === 'add-department' ? 'subject-handled-div' : 'edit-subject-handled-div');

            programSelect.innerHTML = '<option value="">Select Program</option>';

            if (programs[departmentSelect.value]) {
                programs[departmentSelect.value].forEach(program => {
                    let option = document.createElement("option");
                    option.value = program;
                    option.textContent = program;
                    programSelect.appendChild(option);
                });
            }

            subjectHandledDiv.style.display = departmentSelect.value === "BASIC EDUCATION" ? 'block' : 'none';
            
            if (departmentSelect.value !== "BASIC EDUCATION") {
                const subjectSelect = document.getElementById(departmentId === 'add-department' ? 'add-subject-handled' : 'edit-subject-handled');
                if (subjectSelect) {
                    subjectSelect.value = '';
                }
            }
        }

        function handleRoleChange(select, programId) {
            const programSelect = document.getElementById(programId);
            const departmentSelect = document.getElementById(programId === 'add-program' ? 'add-department' : 'edit-department');
            
            if (select.value === 'Subject Coordinator' || 
                (departmentSelect.value === 'BASIC EDUCATION' && select.value === 'Head of Office')) {
                programSelect.required = true;
            } else {
                programSelect.required = false;
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addUserModal');
            const editModal = document.getElementById('editUserModal');
            if (event.target == addModal) {
                closeAddUserModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>

<?php
include "dbcon.php";

/// Insert new category
if (isset($_POST["submit"])) {
    $category = mysqli_real_escape_string($conn, $_POST['category_name']);

    $stmt = $conn->prepare("INSERT INTO `tb_category`(`category_name`, `date_added`) VALUES (?, NOW())");
    $stmt->bind_param("s", $category);

    if ($stmt->execute()) {
      header('Location: main.php?page=category');
      exit();
    } else {
      header('Location: main.php?page=category');
      exit();
    }
}


// Update category
if (isset($_POST['update'])) {
    $categoryid = mysqli_real_escape_string($conn, $_POST['category_id']);
    $category = mysqli_real_escape_string($conn, $_POST['category_name']);

    $stmt = $conn->prepare("UPDATE `tb_category` SET `category_name`=? WHERE `category_id`=?");
    $stmt->bind_param("ss", $category, $categoryid);

    if ($stmt->execute()) {
      header('Location: main.php?page=category');
      exit();
    } else {
      header('Location: main.php?page=category');
      exit();
    }
}

// Delete category
if (isset($_GET["category_id"])) {
    $categoryid = mysqli_real_escape_string($conn, $_GET["category_id"]);
    $sql = "DELETE FROM `tb_category` WHERE `category_id` = '$categoryid'";
    if (mysqli_query($conn, $sql)) {
      header('Location: main.php?page=category');
      exit();
    } else {
      header('Location: main.php?page=category');
      exit();
    }
}
?>


    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manage Categories</title>
    <style>
        /* General Reset */
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
  }
  
  /* Container Styling */
  .container {
    max-width: 900px;
    margin: 50px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  /* Alert Box Styling */
  .alert {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid transparent;
    border-radius: 5px;
    font-size: 0.9rem;
  }
  
  .alert-warning {
    background-color: #fff3cd;
    border-color: #ffeeba;
    color: #856404;
  }
  
  .alert .btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #856404;
    cursor: pointer;
  }
  
  /* Button Styling */
  .btnn{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 10px 20px;
    font-size: 0.9rem;
    color: #ffffff;
    background-color: #712798;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .btnn:hover {
    background-color: #495057;
  }
  
  .btnn-dark {
    margin-bottom: 20px;
  }
  

  .form-controls {
    display: flex;
    gap: 15px;
    align-items: center;
    justify-content: space-between; /* Ensures proper alignment and spaces between the elements */
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

/* Search Form Styling */
.search-form,
.filter-form {
  margin: 0;
}

/* Action Buttons Container */
.action-buttons {
  display: flex;
  gap: 10px; /* Space between buttons */
  align-items: center;
}

/* Search Input and Select Dropdown */
  .search-input,
  .form-select {
    padding: 10px;
    font-size: 0.9rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-width: 200px;
    width: 100%;
  }
  
  .table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .table thead {

    color: #333;
  }
  
  .table th,
  .table td {
    padding: 12px 15px;
    text-align: center;
    font-size: 0.9rem;
    border-bottom: 1px solid #ddd;
  }
  
  .table-hover tbody tr:hover {
    background-color: #ffffff;
  }
  
  .table th {
    font-weight: bold;
  }
  
  .table td {
    color: #555;
  }
  
  .table-dark th {
    background-color: #333;
    color: #fff;
  }
  
  .link-dark {
    color: #343a40;
    text-decoration: none;
    transition: color 0.3s;
  }
  
  .link-dark:hover {
    color: #495057;
  }
  
  .fa-pen-to-square {
    color: #007bff;
    transition: color 0.3s;
  }
  
  .fa-pen-to-square:hover {
    color: #0056b3;
  }
  
  .fa-trash {
    color: #dc3545;
    transition: color 0.3s;
  }
  
  .fa-trash:hover {
    color: #a71d2a;
  }

  /* Remove background, borders, and padding from buttons */
.btn {
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  cursor: pointer;
  color: inherit; /* Inherit color from parent or use your desired color */
}

/* Style for icons inside buttons */
.btn i {
  font-size: 1.2rem; /* Adjust icon size */
  transition: color 0.3s; /* Smooth color transition on hover */
}

.btn i:hover {
  color: #3D1055; /* Darker color on hover */
}

/* Remove text-decoration from links */
a.btn {
  text-decoration: none;
}

  
 

        /* Modal background */
        .modal {
            display: none; /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* Modal content box */
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px; /* Max width for the modal */
            width: 90%; /* Full width but with a max limit */
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            align-items: stretch; /* Stretch children to fill container */
            box-sizing: border-box; /* Include padding in width calculation */
            animation: fadeIn 0.3s ease-in-out; /* Smooth fade-in effect */
        }

        /* Close button */
        .close-btn {
            font-size: 24px;
            color: #333;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #ff5733; /* Change color on hover */
        }

        /* Cancel button */
        .cancel-btn {
            background-color: #f1f1f1;
            color: #333;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .cancel-btn:hover {
            background-color: #ddd; /* Lighten on hover */
        }

        /* Submit button */
        .btn-success {
            background-color: #712798;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #3D1055;
        }

        /* Input fields inside modal */
        .modal-content input,
        .modal-content select { /* Add styles for dropdown */
            width: 100%; /* Full width */
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding in width calculation */
            margin-bottom: 10px; /* Add spacing between input fields */
        }

        /* Styling for the dropdown (select) */
        .modal-content select {
            background-color: #fff;
            color: #333;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .modal-content select:focus {
            border-color: #712798; /* Change border color on focus */
            outline: none; /* Remove default focus outline */
        }

        /* Modal fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .search-container {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 250px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-container select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>


    <div class="container">
        <?php if (isset($_GET["msg"])) {
            $msg = htmlspecialchars($_GET["msg"]);
            echo '<div class="alert alert-warning">' . $msg . '</div>';
        } ?>

        <h1>Categories</h1>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search" onkeyup="filterTable()">
            <button id="openAddModalBtn" class="btnn" onclick="openAddModal()"><i class="fas fa-plus"></i> Add New Category</button>
        </div>
        <table class="table table-hover text-center" id="categoryTable">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT category_id, category_name, date_added FROM `tb_category`";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row["category_id"]) . '</td>
                                <td>' . htmlspecialchars($row["category_name"]) . '</td>
                                <td>' . date("F j, Y", strtotime( $row["date_added"])) . '</td>
                                <td>
                                   <button class="btn" onclick="openEditModal(\'' . $row["category_id"] . '\', \'' . htmlspecialchars($row["category_name"]) . '\')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="category.php?category_id=' . $row["category_id"] . '" class="btn" onclick="return confirm(\'Are you sure?\')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                              </tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No records found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAddModal()">&times;</span>
            <h3>Add New Category</h3>
            <form action="category.php" method="post"  id="addCategoryForm" onsubmit="submitCategoryForm(event)">
                <label>Category:</label>
                <input type="text" name="category_name"  id="addCategory" required>
                <button type="submit" name="submit" class="btn btn-success">Save</button>
                <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h3>Edit Category</h3>
            <form action="" method="post">
                <input type="hidden" name="category_id" id="editCategoryId">
                <label>Category:</label>
                <input type="text" name="category_name" id="editCategory" required>
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addCategoryModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
        }

        function openEditModal(categoryid, category) {
            document.getElementById('editCategoryId').value = categoryid;
            document.getElementById('editCategory').value = category;
            document.getElementById('editCategoryModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editCategoryModal').style.display = 'none';
        }

        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("tb_category");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let td = rows[i].getElementsByTagName("td")[1];
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
        }

        .btn-custom {
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
        }

        .btn-cancel {
            background-color: #ccc;
            color: black;
        }

        .btn-submit {
            background-color: black;
            color: white;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3 class="text-center mb-4">Request Form</h3>

    <form action="/submit-requestDoc" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="date_request" class="form-label">Date:</label>
                <input type="date" class="form-control" name="date_request" id="date_request" required readonly>
            </div>
            <div class="col-md-6">
                <label for="time_request" class="form-label">Time:</label>
                <input type="time" class="form-control" name="time_request" id="time_request" required readonly>
            </div>
        </div>

        <div class="mb-3">
            <label for="type_of_document" class="form-label">Type of Document:</label>
            <select class="form-select" name="type_of_document" id="type_of_document" required>
                <option value="Enrollment Form">Enrollment Form</option>
                <option value="Others">Others</option>
            </select>
        </div>

        <div class="mb-3" id="other_document_container" style="display: none;">
            <label for="other_document" class="form-label">Specify Document:</label>
            <input type="text" class="form-control" name="other_document" id="other_document">
        </div>

        <div class="mb-3">
            <label for="date_required" class="form-label">Date Required:</label>
            <input type="date" class="form-control" name="date_required" required>
        </div>

        <div class="mb-3">
            <label for="paper_size" class="form-label">Paper Size:</label>
            <select class="form-select" name="paper_size" required>
                <option value="Legal">Legal</option>
                <option value="Short">Short</option>
                <option value="Long">Long</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="mode" class="form-label">Mode:</label>
            <select class="form-select" name="mode" required>
                <option value="Print One Sided">Print One Sided</option>
                <option value="Print Both Sides">Print Both Sides</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="number_of_pages" class="form-label">No. of Pages:</label>
                <input type="number" class="form-control" name="number_of_pages" required>
            </div>
            <div class="col-md-6">
                <label for="number_of_copies" class="form-label">No. of Copies:</label>
                <input type="number" class="form-control" name="number_of_copies" required>
            </div>
        </div>

        <div class="mb-3 file-upload">
            <label for="file" class="form-label">Attach Document:</label>
            <input type="file" class="form-control" name="file" accept="application/pdf">
        </div>

        <div class="d-flex justify-content-between">
            <button type="reset" class="btn btn-cancel btn-custom">Cancel</button>
            <button type="submit" class="btn btn-submit btn-custom">Submit</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-fill current date and time
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date();
        document.getElementById('date_request').value = today.toISOString().split('T')[0];

        let hours = today.getHours().toString().padStart(2, '0');
        let minutes = today.getMinutes().toString().padStart(2, '0');
        document.getElementById('time_request').value = hours + ":" + minutes;
    });

    // Show "Other Document" input if "Others" is selected
    document.getElementById('type_of_document').addEventListener('change', function() {
        let otherInput = document.getElementById('other_document_container');
        if (this.value === "Others") {
            otherInput.style.display = "block";
        } else {
            otherInput.style.display = "none";
        }
    });
</script>

</body>
</html>
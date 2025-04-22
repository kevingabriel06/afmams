<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Receipt</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

       

        .container2 {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: auto;
            width: 100%;
        }

        h2 {
            margin-bottom: 15px;
            color: #004085;
            font-weight: 600;
            font-size: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: 0.3s;
            outline: none;
        }

        input:focus {
            border-color: #004085;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.3);
        }

        button {
            background: #0d6efd; /* Blue */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #0b5ed7;
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
            text-align: left;
        }

        .success {
            background-color: #e9f5ff;
            color: #004085;
            border: 1px solid #b8daff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

		.receipt-table {
        width: 100%;
        margin-top: 15px;
        border-collapse: collapse;
        font-size: 14px;
    }

    .receipt-table th, .receipt-table td {
        padding: 12px;
        border: 1px solid #ced4da; /* Add border to each cell */
        text-align: left;
        background: #ffffff;
    }

    .receipt-table th {
        background: #dee2e6; /* Light gray */
        font-weight: 600;
        width: 40%;
    }

    .status-approved {
        display: inline-block;
        background: #d4edda; /* Green */
        color: #155724;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }

        .date-issued {
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container2">
    <h2><i class="fas fa-file-invoice"></i> Verify Registration Receipt</h2>
    
    <input type="text" id="verification_code" placeholder="Enter Verification Code">
    <button onclick="checkReceipt()"><i class="fas fa-search"></i> Check</button>

    <div id="result"></div>
</div>

<script>
    function checkReceipt() {
        const verificationCode = document.getElementById("verification_code").value.trim();
        const resultDiv = document.getElementById("result");

        // Clear previous results
        resultDiv.innerHTML = "";

        if (verificationCode === "") {
            resultDiv.innerHTML = `<div class="message error"><p><i class="fas fa-times-circle"></i> Please enter a verification code.</p></div>`;
            return;
        }

        // Send AJAX request to backend
        fetch("<?= base_url('admin/verify-receipt'); ?>", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "verification_code=" + encodeURIComponent(verificationCode)
        })
        .then(response => response.json())
        .then(data => {
    if (data.status === "success") {  // Check for 'success' instead of 'data.success'
        const receipt = data.data;  // Access the 'data' object
        resultDiv.innerHTML = `
            <div class="message success">
                <h3><i class="fas fa-check-circle"></i> Receipt Verified</h3>
                <table class="receipt-table">
                    <tr>
                        <th><i class="fas fa-id-badge"></i> Student ID</th>
                        <td>${receipt.student_id}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-calendar-alt"></i> Activity</th>
                        <td>${receipt.activity}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-money-bill-wave"></i> Amount Paid</th>
                        <td>${receipt.amount_paid}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-clipboard-check"></i> Status</th>
                        <td><span class="status-approved"> ${receipt.status}</span></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-clock"></i> Date Issued</th>
                        <td>${receipt.date_issued}</td>
                    </tr>
                </table>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `<div class="message error"><p><i class="fas fa-times-circle"></i> ${data.message}</p></div>`;
    }
})

        .catch(error => {
            console.error("Error:", error);
            resultDiv.innerHTML = `<div class="message error"><p><i class="fas fa-times-circle"></i> Error verifying receipt.</p></div>`;
        });
    }
</script>


</body>
</html>

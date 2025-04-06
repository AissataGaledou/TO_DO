let isEmailVerified = false; // Global flag to track if user verified email

// Request OTP
function requestOTP() {
    const email = document.getElementById('email').value.trim();
    const otpMessage = document.getElementById('otp-message');

    if (email === '') {
        otpMessage.textContent = 'Please enter a valid email!';
        otpMessage.style.color = 'red';
        return;
    }

    fetch('opt.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Debugging response
        otpMessage.textContent = data.message;
        otpMessage.style.color = data.success ? 'green' : 'red';
    })
    .catch(error => {
        console.error('Error:', error);
        otpMessage.textContent = 'Error sending OTP. Check console.';
        otpMessage.style.color = 'red';
    });
}

// Verify OTP
function verifyOTP() {
    const email = document.getElementById('email').value.trim();
    const otp = document.getElementById('otp').value.trim();
    const message = document.getElementById('verify-message');

    fetch('verify_opt.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email) + '&otp=' + encodeURIComponent(otp)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Debugging
        message.textContent = data.message;
        message.style.color = data.success ? 'green' : 'red';

        if (data.success) {
            isEmailVerified = true;
            document.getElementById('todo-section').style.display = 'block'; // Show To-Do List
        }
    })
    .catch(error => {
        console.error('Error:', error);
        message.textContent = 'Verification failed. Check console.';
        message.style.color = 'red';
    });
}
// Check if email is verified on page load
function checkEmailVerificationStatus() {
    console.log("Checking email verification status...");
    fetch('check_verification.php')
    .then(response => {
        console.log("Raw response:", response);
        return response.json();
    })
    .then(data => {
        console.log("Verification data received:", data);
        isEmailVerified = data.verified;
        console.log("isEmailVerified set to:", isEmailVerified);
        if (isEmailVerified) {
            document.getElementById('todo-section').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error checking verification status:', error);
    });
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadTasks();
    checkEmailVerificationStatus();
});

// Add Task
function addTask() {
    console.log("Add task clicked, isEmailVerified =", isEmailVerified);

    if (!isEmailVerified) {
        alert('You need to verify your email before adding tasks!');
        return;
    }

    const todoText = document.getElementById('todoText').value.trim();
    const alertElement = document.getElementById('Alert');

    if (todoText === '') {
        alertElement.textContent = 'Please enter a task!';
        alertElement.style.color = 'red';
        return;
    }

    alertElement.textContent = ''; // Clear alert

    fetch('tasks_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add&task=' + encodeURIComponent(todoText)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('todoText').value = '';
            addTaskToList(data.task_id, todoText, false);
            alertElement.style.color = 'green';
            alertElement.textContent = 'Task added successfully!';
            setTimeout(() => { alertElement.textContent = ''; }, 3000);
        } else {
            alertElement.textContent = data.message || 'Error adding task';
            alertElement.style.color = 'red';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertElement.textContent = 'Error: ' + error;
        alertElement.style.color = 'red';
    });
}

// Load Tasks
function loadTasks() {
    fetch('tasks_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=get'
    })
    .then(response => response.json())
    .then(tasks => {
        const listItems = document.getElementById('list-items');
        listItems.innerHTML = '';

        if (Array.isArray(tasks)) {
            tasks.forEach(task => {
                addTaskToList(task.id, task.task, task.completed === '1');
            });
        }
    })
    .catch(error => console.error('Error loading tasks:', error));
}

// Add Task to List
function addTaskToList(id, text, completed) {
    
    const listItems = document.getElementById('list-items');
    const li = document.createElement('li');
    li.setAttribute('data-id', id);

    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = completed;
    checkbox.addEventListener('change', function () {
        toggleTaskStatus(id, this.checked);
    });

    const span = document.createElement('span');
    span.textContent = text;
    if (completed) {
        span.style.textDecoration = 'line-through';
        li.classList.add('completed');
    }

    const deleteButton = document.createElement('button');
    deleteButton.innerHTML = '<span class="material-symbols-outlined">delete</span>';
    deleteButton.className = 'delete-btn';
    deleteButton.addEventListener('click', function () {
        deleteTask(id);
    });

    li.appendChild(checkbox);
    li.appendChild(span);
    li.appendChild(deleteButton);
    listItems.appendChild(li);
}

// Toggle Task Status
function toggleTaskStatus(taskId, completed) {
    fetch('tasks_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=toggle&id=' + encodeURIComponent(taskId) + '&completed=' + (completed ? 1 : 0)
    })
    .then(response => response.text())
    .then(data => console.log('Task status updated:', data))
    .catch(error => console.error('Error updating task:', error));
}

// Delete Task
function deleteTask(taskId) {
    fetch('tasks_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete&id=' + encodeURIComponent(taskId)
    })
    .then(response => response.text())
    .then(data => {
        console.log('Task deleted:', data);
        const li = document.querySelector('li[data-id="' + taskId + '"]');
        if (li) li.remove();
    })
    .catch(error => console.error('Error deleting task:', error));
}


// Example of login success redirect (if part of login process)
function loginUser(email, password) {
    fetch('login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password)
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        if (data.includes('Login successful!')) {
            window.location.href = 'todo.php'; // Redirect
        } else {
            alert('Login failed: ' + data);
        }
    })
    .catch(error => console.error('Error during login:', error));
}
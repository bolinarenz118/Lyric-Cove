<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function e($value)
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$username = $_SESSION['username'] ?? 'User';

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lyric Cove</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: -apple-system, "BlinkMacSystemFont", "Segoe UI", "Roboto", Helvetica, Arial, sans-serif;
    background-image: url('images/bg.jpg');
    background-size: cover; 
    background-attachment: fixed;
    background-repeat: no-repeat;
    color: #1f2937;
}


/* HEADER */

header {
    height: 70px;
    background: rgba(15, 18, 28, 0.75);
    backdrop-filter: blur(25px);
    color: white;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 30px;
}

.logo {
    font-size: 22px;
    font-weight: bold;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.logout {
    background: rgba(224, 0, 0, 0.85);
    color: white;

    text-decoration: none;

    padding: 9px 15px;
    border-radius: 6px;

    font-size: 13px;
}


/* MAIN */

.container {
    max-width: 1400px;
    margin: auto;
    padding: 30px;
}

.intro {
    margin-bottom: 25px;
}

.intro h1 {
    font-size: 26px;
    margin-bottom: 5px;
}

.intro p {
    color: #6b7280;
    font-size: 14px;
}


/* TWO COLUMNS */

.dashboard {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 25px;
}

/* CARD */

.card {
    background: rgba(15, 18, 28, 0.75);
    border-radius: 12px;
    padding: 22px;

    border: 1px solid #e5e7eb;
    
    box-shadow:
        0 3px 10px rgba(0,0,0,.04);
    margin-left: -50px;
    margin-top: 30px;
}

/* SECTION TITLE */


.section-title h2 {
    color: #ffffff; 
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 5px;
}

.section-title p {
    margin: 4px 0 0 0;
    color: rgba(255, 255, 255, 0.75); 
    font-size: 13px;
}

/* CREATE POST */

.create {
    position: sticky;
    top: 20px;
    color: white;
}

.card h2 {
    font-size: 19px;
    margin-bottom: 5px;
}

.subtitle {
    color: white;
    font-size: 12px;
    margin-bottom: 22px;
}


/* FORM */

label {
    display: block;

    font-size: 13px;
    font-weight: bold;

    margin-bottom: 7px;
}

.form-group {
    margin-bottom: 18px;
}

input,
textarea {
    width: 100%;

    border: 1px solid #d1d5db;
    background: rgba(15, 18, 28, 0.75);
    border-radius: 7px;

    padding: 11px;

    font-size: 14px;
    color: white;
    outline: none;
}

input:focus,
textarea:focus {
    border-color: #2563eb;
}

textarea {
    height: 230px;
    resize: vertical;
}


/* BUTTONS */

button {
    border: none;
    border-radius: 6px;

    padding: 10px 15px;

    font-weight: bold;
    cursor: pointer;
}

.primary {  
    background: rgba(0, 102, 255, 0.85);
    color: white;
    border-radius: 20px;
}

.cancel {
    background: rgba(255, 255, 255, 0.85);
    color: #374151;
}

.edit {
    background: rgba(255, 102, 0, 0.85);
    color: white;
}

.delete {
    background: rgba(224, 0, 0, 0.85);
    color: white;
}


/* MESSAGE */

.message {
    display: none;

    padding: 10px;

    margin-bottom: 15px;

    border-radius: 6px;

    font-size: 13px;
}


/* POSTS */

.posts-title {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-bottom: 20px;    
}

.posts-title > div {
    dsipaly: inline-block;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.posts-title h2 {
    margin: 0;
    color: #ffffff;
    font-size: 22px;
    font-weight: 700;
}

.posts-title .subtitle{
    margin: 4px 0 0 0;
    color: rgba(255, 255, 255, 0.75);
    font-size: 13px;    
}

.posts {
    display: flex;
    flex-direction: column;
    gap: 25px;
}


/* POST CARD */

.post {
    background: rgba(15, 18, 28, 0.75);

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 20px;
}

.post-header {
    display: flex;
    justify-content: space-between;

    margin-bottom: 1px;
}

.post-title {
    font-size: 18px;
    font-weight: bold;
    color: white;
    word-break: break-word;
}

.post-id {
    color: white;
    font-size: 11px;
}

.post-content {
    color: white;

    font-size: 14px;

    line-height: 1.7;

    white-space: pre-line;

    margin-bottom: 17px;
}

.post-footer {
    border-top: 1px solid #eee;

    padding-top: 12px;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.author {
    color: white;
    font-size: 12px;
}

.actions {
    display: flex;
    gap: 6px;
}

.actions button {
    padding: 7px 11px;
    font-size: 11px;
}


/* EMPTY */

.empty {
    background: rgba(15, 18, 28, 0.75);

    border: 1px dashed #d1d5db;

    border-radius: 20px;

    padding: 50px;

    text-align: center;

    color: white;
}


/* MOBILE */

@media (max-width: 800px) {

    .dashboard {
        grid-template-columns: 1fr;
    }

    .create {
        position: static;
    }

}

@media (max-width: 600px) {

    header {
        padding: 0 15px;
    }

    .container {
        padding: 20px 15px;
    }

    .dashboard {
        gap: 20px;
    }

    .header-right span {
        display: none;
    }

}

</style>

</head>


<body>


<!-- HEADER -->

<header>

    <div class="logo">
        Lyric Cove
    </div>

    <div class="header-right">

        <span>
            Welcome, <strong><?= e($username) ?></strong>
        </span>

        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</header>



<!-- MAIN -->

<div class="container">


    <!-- TWO COLUMNS -->

    <div class="dashboard">


        <!-- LEFT SIDE -->
    <div>

        <div class="card create" id="createCard">

            <h2 id="formTitle">
                Compose Track
            </h2>

            <p class="subtitle">
                Create a new track.
            </p>


            <div
                id="message"
                class="message"
            ></div>


            <form id="postForm">


                <input
                    type="hidden"
                    id="postId"
                >


                <div class="form-group">

                    <label for="title">
                        Track Name
                    </label>

                    <input
                        type="text"
                        id="title"
                        placeholder="Track Name..."
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="content">
                        Lyrics/Verses
                    </label>

                    <textarea
                        id="content"
                        placeholder="Write your lyrics/verses..."
                        required
                    ></textarea>

                </div>


                <button
                    type="submit"
                    class="primary"
                    id="saveButton"
                >
                    Create Track
                </button>


                <button
                    type="button"
                    class="cancel"
                    id="cancelButton"
                    style="display:none;"
                >
                    Cancel
                </button>

            </form>

        </div>

    </div>

        <!-- RIGHT SIDE -->

        <div>

            <div class="posts-title">

                <div>

                    <h2>
                        Track List
                    </h2>

                    <p class="subtitle">
                        All your compositions are shown below.
                    </p>

                </div>

            </div>


            <div
                id="posts"
                class="posts"
            >

                <div class="empty">
                    Loading posts...
                </div>

            </div>

        </div>


    </div>

</div>



<script>

/* ELEMENTS */

const form =
    document.getElementById('postForm');

const postId =
    document.getElementById('postId');

const title =
    document.getElementById('title');

const content =
    document.getElementById('content');

const posts =
    document.getElementById('posts');

const message =
    document.getElementById('message');

const saveButton =
    document.getElementById('saveButton');

const cancelButton =
    document.getElementById('cancelButton');

const formTitle =
    document.getElementById('formTitle');

const csrfToken =
    sessionStorage.getItem('csrf_token');


/* ESCAPE HTML */

function escapeHtml(value) {

    const div =
        document.createElement('div');

    div.textContent =
        value ?? '';

    return div.innerHTML;
}


/* MESSAGE */

function showMessage(text, error = false) {

    message.textContent =
        text;

    message.style.display =
        'block';

    message.style.background =
        error ? '#fef2f2' : '#ecfdf5';

    message.style.color =
        error ? '#b91c1c' : '#047857';

}


/* LOAD POSTS */

async function loadPosts() {

    try {

        const response =
            await fetch('api/posts.php');


        if (response.status === 401) {

            sessionStorage.removeItem(
                'csrf_token'
            );

            location.href =
                'login.php';

            return;
        }


        const data =
            await response.json();


        if (!response.ok) {

            throw new Error(
                data.message ||
                'Failed to load posts.'
            );

        }


        const postList =
            data.posts || [];


        posts.innerHTML =
            '';


        if (postList.length === 0) {

            posts.innerHTML = `

                <div class="empty">

                    No tracks yet.

                </div>

            `;

            return;
        }


        postList.forEach(post => {

            const item =
                document.createElement('div');

            item.className =
                'post';


            item.innerHTML = `

                <div class="post-header">

                    <div class="post-title">

                        ${escapeHtml(
                            post.title
                        )}

                    </div>

                    <div class="post-id">

                        #${escapeHtml(
                            String(post.id)
                        )}

                    </div>

                </div>


                <div class="post-content">

                    ${escapeHtml(
                        post.content
                    )}

                </div>


                <div class="post-footer">

                    <div class="author">

                        Composer:
                        <strong>
                            ${escapeHtml(
                                post.author ||
                                'Unknown'
                            )}
                        </strong>

                    </div>


                    <div class="actions">

                        <button
                            class="edit"
                            onclick="editPost(${Number(post.id)})"
                        >
                            Edit
                        </button>


                        <button
                            class="delete"
                            onclick="deletePost(${Number(post.id)})"
                        >
                            Delete
                        </button>

                    </div>

                </div>

            `;


            posts.appendChild(item);

        });

    }

    catch (error) {

        posts.innerHTML = `

            <div class="empty">

                Unable to load posts.

            </div>

        `;

    }

}


/* CREATE / UPDATE */

form.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();


        const id =
            postId.value;


        const data = {

            title:
                title.value.trim(),

            content:
                content.value.trim()

        };


        if (!data.title || !data.content) {

            showMessage(
                'Please fill in all fields.',
                true
            );

            return;
        }


        const editing =
            id !== '';


        const url =
            editing
                ? `api/posts.php?id=${encodeURIComponent(id)}`
                : 'api/posts.php';


        const method =
            editing
                ? 'PUT'
                : 'POST';


        try {

            saveButton.disabled =
                true;


            saveButton.textContent =
                editing
                    ? 'Updating...'
                    : 'Creating...';


            const response =
                await fetch(
                    url,
                    {

                        method: method,

                        headers: {

                            'Content-Type':
                                'application/json',

                            'X-CSRF-Token':
                                csrfToken

                        },

                        body:
                            JSON.stringify(data)

                    }
                );


            if (response.status === 401) {

                sessionStorage.removeItem(
                    'csrf_token'
                );

                location.href =
                    'login.php';

                return;
            }


            const result =
                await response.json();


            if (!response.ok) {

                showMessage(
                    result.message ||
                    'Operation failed.',
                    true
                );

                return;
            }


            showMessage(
                editing
                    ? 'Track updated successfully!'
                    : 'Track created successfully!'
            );


            resetForm();

            loadPosts();

        }

        catch (error) {

            showMessage(
                'Unable to connect to the server.',
                true
            );

        }

        finally {

            saveButton.disabled =
                false;

            saveButton.textContent =
                'Create Track';

        }

    }
);


async function editPost(id) {

    try {

        const response =
            await fetch(
                `api/posts.php?id=${encodeURIComponent(id)}`
            );


        const data =
            await response.json();


        if (!response.ok) {

            showMessage(
                data.message ||
                'Unable to load post.',
                true
            );

            return;
        }


        postId.value =
            data.post.id;

        title.value =
            data.post.title;

        content.value =
            data.post.content;


        formTitle.textContent =
            'Edit Track';


        saveButton.textContent =
            'Update Track';


        cancelButton.style.display =
            'inline-block';


        document
            .getElementById('createCard')
            .scrollIntoView({
                behavior: 'smooth'
            });

    }

    catch (error) {

        showMessage(
            'Unable to load post.',
            true
        );

    }

}

async function deletePost(id) {

    if (
        !confirm(
            'Are you sure you want to delete this track?'
        )
    ) {
        return;
    }


    try {

        const response =
            await fetch(
                `api/posts.php?id=${encodeURIComponent(id)}`,
                {

                    method: 'DELETE',

                    headers: {

                        'X-CSRF-Token':
                            csrfToken

                    }

                }
            );


        const data =
            await response.json();


        if (!response.ok) {

            showMessage(
                data.message ||
                'Delete failed.',
                true
            );

            return;
        }


        showMessage(
            'Track deleted successfully!'
        );


        loadPosts();

    }

    catch (error) {

        showMessage(
            'Unable to connect to the server.',
            true
        );

    }

}

function resetForm() {

    postId.value =
        '';

    title.value =
        '';

    content.value =
        '';

    formTitle.textContent =
        'Create Track';

    saveButton.textContent =
        'Create Track';

    cancelButton.style.display =
        'none';

}


cancelButton.addEventListener(
    'click',
    resetForm
);

loadPosts();

</script>


</body>

</html>
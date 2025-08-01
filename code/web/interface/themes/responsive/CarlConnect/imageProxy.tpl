{strip}
<div id="main-content" class="col-md-12">
    <h1>CarlConnect Authentication</h1>
    <p>
        The image you are trying to view requires authentication with CarlConnect.
        Please enter your CarlConnect credentials below to access the image.
    </p>

    {if isset($error)}
        <div class="alert alert-danger">
            {$error}
        </div>
    {/if}

    <form method="post" action="{$returnUrl}" class="form">
        <input type="hidden" name="url" value="{$imageUrl}">

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="/Report/LibrarianFacebook" class="btn btn-default">Cancel</a>
        </div>

        <div class="alert alert-info">
            <p>
                <strong>Note:</strong> Your credentials will be stored in your session to avoid
                having to log in multiple times. They will be cleared when you close your browser
                or log out of Aspen Discovery.
            </p>
        </div>
    </form>
</div>
{/strip}

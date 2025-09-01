function handleDeleteHolding(id) {
    if (typeof id !== 'number' || id <= 0) {
        alert("Invalid post ID.");
        return;
    }

    if (confirm("Are you sure you want to delete this holding?")) {
        fetch(`/holdings/delete?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Server error: ${response.status} - ${text}`);
                });
            }
        })
        .catch(error => {
            window.location.href = `/redirect?message=${encodeURIComponent(error.message)}&message_type=error`;
        });
    }
}

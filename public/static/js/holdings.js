function handleDeleteHolding(id) {
    if (typeof id !== 'number' || id <= 0) {
        alert("Invalid post ID.");
        return;
    }

    if (confirm("Are you sure you want to delete this holding?")) {
        fetch(`/holdings/delete?post_id=${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/redirect?message=Holding deleted successfully&message_type=success';
            } else {
                alert("Error deleting holding: " + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
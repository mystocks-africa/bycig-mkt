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
        .then(() => {
                window.location.href = '/redirect?message=Holding deleted successfully&message_type=success';
        })
        .catch(error => {
            alert(error.message)
        });
    }
}
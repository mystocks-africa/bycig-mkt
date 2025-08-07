function handleDeleteProposal(postId) {
    if (confirm("Are you sure you want to delete this proposal? This action cannot be undone.")) {
        const formData = new FormData();
        formData.append('post_id', postId);

        fetch('/admin/delete-proposal', {
            method: 'DELETE',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Proposal deleted successfully.');
                location.reload();
            } else {
                alert('Error deleting proposal: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred while deleting the proposal.');
        });
    }
}
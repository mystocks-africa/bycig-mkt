function handleAddNavItem(e, navItemContainer, navItems) {
    e.preventDefault();
    navItems.forEach(item => {
        const li = document.createElement('li');
        if (item.isButton) {
            const form = document.createElement('form');
            form.action = item.href;
            form.method = 'POST';

            const button = document.createElement('button');
            button.type = 'submit';
            button.id = 'signout-btn';
            button.textContent = item.text;

            form.appendChild(button);
            li.appendChild(form);
        } else {
            const a = document.createElement('a');
            a.href = item.href;
            a.textContent = item.text;
            li.appendChild(a);
        }
        navItemContainer.appendChild(li);
    })
}

document.addEventListener('DOMContentLoaded', (event) => {
    const navItemContainer = document.querySelector('.nav-links');
    const isAuthenticated = Boolean(window.serverData.sessionCookie);

    let navItems = [ ]

    if (isAuthenticated) {
        navItems.push(
            { text: 'User Profile', href: '/profile' },
            { text: 'Create a proposal', href: '/proposals/submit' },
            { text: 'Sign out', href: '/auth/signout', isButton: true }
        )
    } else {
        navItems.push(
            { text: 'Sign In', href: '/auth/signin' },
            { text: 'Sign Up', href: '/auth/signup' },
            { text: 'Forgot password?', href: '/auth/forgot-pwd' }
        );
    }

    handleAddNavItem(event, navItemContainer, navItems);
});

const toggleButton = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

toggleButton.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    toggleButton.classList.toggle('active');
});
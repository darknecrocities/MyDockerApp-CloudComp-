document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('player-form');
    const statusMessage = document.getElementById('status-message');
    const playersTbody = document.getElementById('players-tbody');
    const playerCount = document.getElementById('player-count');
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');

    /**
     * Show a status message with animation
     */
    function showMessage(text, type) {
        statusMessage.textContent = text;
        statusMessage.className = `message ${type} show`;

        setTimeout(() => {
            statusMessage.classList.remove('show');
        }, 3500);
    }

    /**
     * Fetch all players from the API and refresh the table
     */
    async function refreshPlayers() {
        try {
            const res = await fetch('index.php?api=players', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success) {
                renderPlayers(data.players);
            }
        } catch (err) {
            console.error('Failed to refresh players:', err);
        }
    }

    /**
     * Render the players into the table
     */
    function renderPlayers(players) {
        playerCount.textContent = players.length;

        if (players.length === 0) {
            playersTbody.innerHTML = `
                <tr class="empty-row">
                    <td colspan="2">No players registered yet.</td>
                </tr>`;
            return;
        }

        playersTbody.innerHTML = players.map((p, i) => `
            <tr class="fade-in" style="animation-delay: ${i * 0.05}s">
                <td><span class="id-badge">#${p.id}</span></td>
                <td>${escapeHtml(p.name)}</td>
            </tr>
        `).join('');
    }

    /**
     * Simple HTML escape
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /**
     * Handle form submission via fetch (AJAX)
     */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!name || !password) {
            showMessage('Name and password are required.', 'error');
            return;
        }

        // Disable button during submission
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';

        try {
            const res = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, password })
            });

            const data = await res.json();

            if (data.success) {
                showMessage(data.message, 'success');
                form.reset();
                await refreshPlayers();
            } else {
                showMessage(data.message, 'error');
            }
        } catch (err) {
            showMessage('Network error. Please try again.', 'error');
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }
    });

    // Auto-hide PHP-rendered messages after a delay
    if (statusMessage.classList.contains('show')) {
        setTimeout(() => {
            statusMessage.classList.remove('show');
        }, 3500);
    }
});

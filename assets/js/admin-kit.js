import TomSelect from 'tom-select';
import '../styles/kit.scss';

// ---- Initialisation des selects avec recherche AJAX ----
const adminSelects = {};

function initAdminSelect(type, apiUrl) {
    const el = document.getElementById('admin-' + type + '-select');
    if (!el) return;

    adminSelects[type] = new TomSelect(el, {
        valueField: 'id',
        labelField: 'text',
        searchField: 'text',
        placeholder: 'Tapez pour chercher…',
        preload: false,
        load(query, callback) {
            if (query.length < 2) { callback(); return; }
            fetch(apiUrl + '?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
        },
        render: {
            no_results() {
                return '<div class="no-results">Aucun résultat</div>';
            },
        },
    });
}

// ---- Ajout d'un élément au kit d'un utilisateur (admin) ----
function adminAddToKit(type, userId) {
    const sel = adminSelects[type];
    const id  = sel ? sel.getValue() : null;
    const qty  = document.getElementById('admin-' + type + '-qty').value;
    const note = document.getElementById('admin-' + type + '-note').value;

    if (!id) { alert('Veuillez sélectionner un élément'); return; }

    const body = new URLSearchParams();
    body.append(type + '_id', id);
    body.append('quantity', qty);
    body.append('note', note);

    fetch(`/admin/kits/user/${userId}/${type}/add`, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.success) { alert(data.message); window.location.reload(); }
            else { alert('Erreur : ' + data.message); }
        })
        .catch(() => alert('Une erreur est survenue'));
}

// ---- Initialisation au chargement ----
document.addEventListener('DOMContentLoaded', () => {
    const config = document.getElementById('admin-kit-config');
    if (!config) return;

    const userId = config.dataset.userId;

    initAdminSelect('game',      config.dataset.gamesUrl);
    initAdminSelect('console',   config.dataset.consolesUrl);
    initAdminSelect('accessory', config.dataset.accessoriesUrl);

    // Boutons Ajouter
    document.querySelectorAll('.admin-kit-add-btn').forEach(btn => {
        btn.addEventListener('click', () => adminAddToKit(btn.dataset.type, userId));
    });

    // Boutons Sauvegarder
    document.querySelectorAll('.update-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const type = this.dataset.type;
            const id   = this.dataset.id;
            const row  = this.closest('tr');
            const qty  = row.querySelector('.quantity-input').value;
            const note = row.querySelector('.note-input').value;

            fetch(`/admin/kits/${type}/${id}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `quantity=${qty}&note=${encodeURIComponent(note)}`,
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { alert('Kit mis à jour !'); }
                    else { alert('Erreur : ' + data.message); }
                });
        });
    });

    // Boutons Supprimer
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Supprimer cet élément du kit ?')) return;
            const type = this.dataset.type;
            const id   = this.dataset.id;
            const row  = this.closest('tr');

            fetch(`/admin/kits/${type}/${id}/delete`, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { row.remove(); }
                    else { alert('Erreur : ' + data.message); }
                });
        });
    });
});


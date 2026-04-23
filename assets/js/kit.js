import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.min.css';
import '../styles/kit.scss';

// ============================================================
// TOM-SELECT — initialisation avec recherche AJAX
// ============================================================

const kitSelects = {};

function initKitSelect(type, apiUrl) {
    const el = document.getElementById(type + '-select');
    if (!el) return;

    kitSelects[type] = new TomSelect(el, {
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
                return '<div class="no-results">Aucun résultat trouvé</div>';
            },
            loading() {
                return '<div class="loading">Recherche en cours…</div>';
            },
        },
    });
}

// ============================================================
// ONGLETS DE TYPE (Jeu / Console / Accessoire)
// ============================================================

function initTypeTabs() {
    const tabs = document.querySelectorAll('.kit-type-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.target;

            tabs.forEach(t => t.classList.remove('kit-type-tab--active'));
            document.querySelectorAll('.kit-add-type').forEach(p => p.classList.remove('kit-add-type--active'));

            tab.classList.add('kit-type-tab--active');
            const panel = document.getElementById('add-' + target);
            if (panel) panel.classList.add('kit-add-type--active');
        });
    });
}

// ============================================================
// ONGLETS DE LISTE (Jeux / Consoles / Accessoires)
// ============================================================

function initListTabs() {
    document.querySelectorAll('.coll-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.dataset.tab;
            document.querySelectorAll('.coll-tab').forEach(t => t.classList.remove('coll-tab--active'));
            document.querySelectorAll('.coll-panel').forEach(p => p.classList.remove('coll-panel--active'));
            this.classList.add('coll-tab--active');
            const panel = document.getElementById('tab-' + target);
            if (panel) panel.classList.add('coll-panel--active');
        });
    });
}

// ============================================================
// AJOUT D'UN ÉLÉMENT AU KIT
// ============================================================

function addToKit(type) {
    const sel  = kitSelects[type];
    const id   = sel ? sel.getValue() : null;
    const qty  = document.getElementById(type + '-qty-input')?.value;
    const note = document.getElementById(type + '-note-input')?.value;

    if (!id) {
        alert('Veuillez sélectionner un élément dans la liste de recherche');
        return;
    }

    const body = new URLSearchParams();
    body.append(type + '_id', id);
    body.append('quantity', qty || 1);
    body.append('note', note || '');

    fetch(`/kit/${type}/add`, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            if (data.success) { window.location.reload(); }
            else { alert('Erreur : ' + data.message); }
        })
        .catch(() => alert('Une erreur est survenue'));
}

// ============================================================
// SAUVEGARDE & SUPPRESSION
// ============================================================

function initCardActions() {
    document.querySelectorAll('.update-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const type = this.dataset.type;
            const id   = this.dataset.id;
            const card = this.closest('[data-kit-id]');
            const qty  = card.querySelector('.quantity-input').value;
            const note = card.querySelector('.note-input').value;
            const orig = this.innerHTML;

            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled  = true;

            fetch(`/kit/${type}/${id}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `quantity=${qty}&note=${encodeURIComponent(note)}`,
            })
                .then(r => r.json())
                .then(data => {
                    this.innerHTML = data.success ? '<i class="fas fa-check"></i> Sauvegardé' : orig;
                    setTimeout(() => { this.innerHTML = orig; this.disabled = false; }, 2000);
                })
                .catch(() => { this.innerHTML = orig; this.disabled = false; });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Retirer cet élément de votre kit ?')) return;
            const type = this.dataset.type;
            const id   = this.dataset.id;
            const card = this.closest('[data-kit-id]');

            fetch(`/kit/${type}/${id}/delete`, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                        card.style.opacity    = '0';
                        card.style.transform  = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                });
        });
    });
}

// ============================================================
// INIT
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    const config = document.getElementById('kit-config');
    if (!config) return;

    initKitSelect('game',      config.dataset.gamesUrl);
    initKitSelect('console',   config.dataset.consolesUrl);
    initKitSelect('accessory', config.dataset.accessoriesUrl);

    initTypeTabs();
    initListTabs();
    initCardActions();

    // Boutons Ajouter
    document.querySelectorAll('.kit-add-btn').forEach(btn => {
        btn.addEventListener('click', () => addToKit(btn.dataset.type));
    });
});


// API Service - VIP School REST Client
if (typeof API !== 'undefined' && API.utilisateurs) { /* already loaded */ } else {

window.API = {
  get base_url() { return typeof BASE_URL !== 'undefined' ? BASE_URL : (window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '') + window.location.pathname.replace(/\/[^/]*$/, '/')); },

  async request(method, endpoint, data = null) {
    const url = this.base_url + endpoint;
    const options = {
      method: method,
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    };
    if (data && (method === 'POST' || method === 'PUT')) {
      options.body = JSON.stringify(data);
    }
    try {
      const response = await fetch(url, options);
      return await response.json();
    } catch (error) {
      console.error('API Error:', error);
      return { success: false, message: 'Erreur de connexion au serveur' };
    }
  },

  get(endpoint) { return this.request('GET', endpoint); },
  post(endpoint, data) { return this.request('POST', endpoint, data); },
  put(endpoint, data) { return this.request('PUT', endpoint, data); },
  delete(endpoint) { return this.request('DELETE', endpoint); },

  utilisateurs: { list: () => API.get('api/utilisateurs'), get: (id) => API.get('api/utilisateurs/' + id), create: (d) => API.post('api/utilisateurs/create', d), update: (id, d) => API.post('api/utilisateurs/' + id + '/update', d), delete: (id) => API.get('api/utilisateurs/' + id + '/delete') },
  roles: { list: () => API.get('api/roles'), get: (id) => API.get('api/roles/' + id), create: (d) => API.post('api/roles/create', d), update: (id, d) => API.post('api/roles/' + id + '/update', d), delete: (id) => API.get('api/roles/' + id + '/delete') },
  menus: { list: () => API.get('api/menus'), get: (id) => API.get('api/menus/' + id), create: (d) => API.post('api/menus/create', d), update: (id, d) => API.post('api/menus/' + id + '/update', d), delete: (id) => API.get('api/menus/' + id + '/delete') },
  etudiants: { list: () => API.get('api/etudiants'), get: (id) => API.get('api/etudiants/' + id), create: (d) => API.post('api/etudiants/create', d), update: (id, d) => API.post('api/etudiants/' + id + '/update', d), delete: (id) => API.get('api/etudiants/' + id + '/delete'), uploadPhoto: (formData) => { const url = API.base_url + 'api/etudiants/upload_photo'; return fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()); } },
  inscriptions: { list: () => API.get('api/inscriptions'), get: (id) => API.get('api/inscriptions/' + id), create: (d) => API.post('api/inscriptions/create', d), update: (id, d) => API.post('api/inscriptions/' + id + '/update', d), delete: (id) => API.get('api/inscriptions/' + id + '/delete') },
  enseignants: { list: () => API.get('api/enseignants'), get: (id) => API.get('api/enseignants/' + id), create: (d) => API.post('api/enseignants/create', d), update: (id, d) => API.post('api/enseignants/' + id + '/update', d), delete: (id) => API.get('api/enseignants/' + id + '/delete') },
  classes: { list: () => API.get('api/classes'), get: (id) => API.get('api/classes/' + id), create: (d) => API.post('api/classes/create', d), update: (id, d) => API.post('api/classes/' + id + '/update', d), delete: (id) => API.get('api/classes/' + id + '/delete') },
   sections: { list: () => API.get('api/sections'), get: (id) => API.get('api/sections/' + id), create: (d) => API.post('api/sections/create', d), update: (id, d) => API.post('api/sections/' + id + '/update', d), delete: (id) => API.get('api/sections/' + id + '/delete'), activate: (id) => API.get('api/sections/' + id + '/activate'), deactivate: (id) => API.get('api/sections/' + id + '/deactivate') },
  matieres: { list: () => API.get('api/matieres'), get: (id) => API.get('api/matieres/' + id), create: (d) => API.post('api/matieres/create', d), update: (id, d) => API.post('api/matieres/' + id + '/update', d), delete: (id) => API.get('api/matieres/' + id + '/delete') },
   periodes: { list: () => API.get('api/periodes'), get: (id) => API.get('api/periodes/' + id), create: (d) => API.post('api/periodes/create', d), update: (id, d) => API.post('api/periodes/' + id + '/update', d), delete: (id) => API.get('api/periodes/' + id + '/delete'), activate: (id) => API.get('api/periodes/' + id + '/activate'), deactivate: (id) => API.get('api/periodes/' + id + '/deactivate'), setActive: (id) => API.get('api/periodes/' + id + '/active') },
   matieres_classes: { list: () => API.get('api/matieres_classes'), get: (id) => API.get('api/matieres_classes/' + id), create: (d) => API.post('api/matieres_classes/create', d), update: (id, d) => API.post('api/matieres_classes/' + id + '/update', d), delete: (id) => API.get('api/matieres_classes/' + id + '/delete') },
  enseignements: { list: () => API.get('api/enseignements'), get: (id) => API.get('api/enseignements/' + id), create: (d) => API.post('api/enseignements/create', d), update: (id, d) => API.post('api/enseignements/' + id + '/update', d), delete: (id) => API.get('api/enseignements/' + id + '/delete') },
  frais: { list: () => API.get('api/frais'), get: (id) => API.get('api/frais/' + id), create: (d) => API.post('api/frais/create', d), update: (id, d) => API.post('api/frais/' + id + '/update', d), delete: (id) => API.get('api/frais/' + id + '/delete') },
   paiements: { list: () => API.get('api/paiements_data'), get: (id) => API.get('api/paiements_data/' + id), create: (d) => API.post('api/paiements_data/create', d), update: (id, d) => API.post('api/paiements_data/' + id + '/update', d), delete: (id) => API.get('api/paiements_data/' + id + '/delete') },
    notes: {
        list: () => API.get('api/notes'),
        create: (d) => API.post('api/notes/create', d),
        delete: (id) => API.get('api/notes/' + id + '/delete')
    },
    evaluations: {
        list: () => API.get('api/evaluations'),
        create: (d) => API.post('api/evaluations/create', d),
        update: (id, d) => API.post('api/evaluations/' + id + '/update', d),
        delete: (id) => API.get('api/evaluations/' + id + '/delete')
    },
    bulletins: {
        list: () => API.get('api/bulletins'),
        create: (d) => API.post('api/bulletins/create', d),
        update: (id, d) => API.post('api/bulletins/' + id + '/update', d),
        delete: (id) => API.get('api/bulletins/' + id + '/delete'),
        generer: (d) => API.post('api/bulletins/generer', d)
    },
   produits: { list: () => API.get('api/produits'), get: (id) => API.get('api/produits/' + id), create: (d) => API.post('api/produits/create', d), update: (id, d) => API.post('api/produits/' + id + '/update', d), delete: (id) => API.get('api/produits/' + id + '/delete') },
   categories: { list: () => API.get('api/categories'), get: (id) => API.get('api/categories/' + id), create: (d) => API.post('api/categories/create', d), update: (id, d) => API.post('api/categories/' + id + '/update', d), delete: (id) => API.get('api/categories/' + id + '/delete') },
   mouvements: { list: () => API.get('api/mouvements'), create: (d) => API.post('api/mouvements/create', d) },
   commandes: { list: () => API.get('api/commandes'), get: (id) => API.get('api/commandes/' + id), create: (d) => API.post('api/commandes/create', d), update: (id, d) => API.post('api/commandes/' + id + '/update', d), delete: (id) => API.get('api/commandes/' + id + '/delete') },
   horaires: { list: () => API.get('api/horaires'), create: (d) => API.post('api/horaires/create', d), update: (id, d) => API.post('api/horaires/' + id + '/update', d), delete: (id) => API.get('api/horaires/' + id + '/delete'), generer: () => API.post('api/horaires/generer') },

  parametres: { list: () => API.get('api/parametres'), update: (d) => API.post('api/parametres/update', d) },
   evenements: { list: () => API.get('api/evenements'), create: (d) => API.post('api/evenements/create', d), update: (id, d) => API.post('api/evenements/' + id + '/update', d), delete: (id) => API.get('api/evenements/' + id + '/delete') },
    assurances: { list: () => API.get('api/assurances'), get: (id) => API.get('api/assurances/' + id), create: (d) => API.post('api/assurances/create', d), update: (id, d) => API.post('api/assurances/' + id + '/update', d), delete: (id) => API.get('api/assurances/' + id + '/delete') },
   creneaux: { list: () => API.get('api/creneaux'), get: (id) => API.get('api/creneaux/' + id), create: (d) => API.post('api/creneaux/create', d), update: (id, d) => API.post('api/creneaux/' + id + '/update', d), delete: (id) => API.get('api/creneaux/' + id + '/delete') },
   disponibilites: { list: () => API.get('api/disponibilites'), get: (id) => API.get('api/disponibilites/' + id), create: (d) => API.post('api/disponibilites/create', d), update: (id, d) => API.post('api/disponibilites/' + id + '/update', d), delete: (id) => API.get('api/disponibilites/' + id + '/delete') },
   uniformes: { list: () => API.get('api/uniformes'), get: (id) => API.get('api/uniformes/' + id), create: (d) => API.post('api/uniformes/create', d), update: (id, d) => API.post('api/uniformes/' + id + '/update', d), delete: (id) => API.get('api/uniformes/' + id + '/delete') },
   audit: { list: () => API.get('api/audit') },
   notifications: { list: () => API.get('api/notifications'), markRead: (id) => API.get('api/notifications/' + id + '/read') },
  annees: { list: () => API.get('api/annees'), get: (id) => API.get('api/annees/' + id), create: (d) => API.post('api/annees/create', d), update: (id, d) => API.post('api/annees/' + id + '/update', d), delete: (id) => API.get('api/annees/' + id + '/delete'), activate: (id) => API.get('api/annees/' + id + '/activate'), deactivate: (id) => API.get('api/annees/' + id + '/deactivate'), setActive: (id) => API.get('api/annees/' + id + '/active') },
  sauvegardes: { create: () => API.post('api/sauvegardes/create'), list: () => API.get('api/sauvegardes/list'), download: (f) => API.get('api/sauvegardes/download/' + f), delete: (f) => API.get('api/sauvegardes/delete/' + f) },
    operations: { tables: () => API.get('api/operations/tables'), export: (t) => API.get('api/operations/export/' + t), preview: (t) => API.get('api/operations/preview/' + t) },

   recus: { list: () => API.get('api/recus'), get: (id) => API.get('api/recus/' + id), create: (d) => API.post('api/recus/create', d), update: (id, d) => API.post('api/recus/' + id + '/update', d), delete: (id) => API.get('api/recus/' + id + '/delete') },
   echeances: { list: () => API.get('api/echeances'), get: (id) => API.get('api/echeances/' + id), create: (d) => API.post('api/echeances/create', d), update: (id, d) => API.post('api/echeances/' + id + '/update', d), delete: (id) => API.get('api/echeances/' + id + '/delete') },
   types_frais: { list: () => API.get('api/types_frais'), get: (id) => API.get('api/types_frais/' + id), create: (d) => API.post('api/types_frais/create', d), update: (id, d) => API.post('api/types_frais/' + id + '/update', d), delete: (id) => API.get('api/types_frais/' + id + '/delete') },
   commandes_details: { list: () => API.get('api/commandes_details'), get: (id) => API.get('api/commandes_details/' + id), create: (d) => API.post('api/commandes_details/create', d), update: (id, d) => API.post('api/commandes_details/' + id + '/update', d), delete: (id) => API.get('api/commandes_details/' + id + '/delete') },
   paiements_recus: { list: () => API.get('api/paiements_recus'), create: (d) => API.post('api/paiements_recus/create', d), delete: (id) => API.get('api/paiements_recus/' + id + '/delete') },
   toilettes: { list: () => API.get('api/toilettes'), get: (id) => API.get('api/toilettes/' + id), create: (d) => API.post('api/toilettes/create', d), update: (id, d) => API.post('api/toilettes/' + id + '/update', d), delete: (id) => API.get('api/toilettes/' + id + '/delete') },
   librairie: { list: () => API.get('api/librairie'), get: (id) => API.get('api/librairie/' + id), create: (d) => API.post('api/librairie/create', d), update: (id, d) => API.post('api/librairie/' + id + '/update', d), delete: (id) => API.get('api/librairie/' + id + '/delete'), initialiser: () => API.post('api/librairie/initialiser') }
};

API.paiements_data = API.paiements;

}

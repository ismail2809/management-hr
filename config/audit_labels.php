<?php

return [

    /*
     * Labels lisibles pour les modules (log_name).
     */
    'modules' => [
        'employee'          => 'Employé',
        'leave'             => 'Congé',
        'document_request'  => 'Demande de document',
        'employee_document' => 'Document employé',
        'user'              => 'Utilisateur',
        'company'           => 'Entreprise',
        'profession'        => 'Profession',
        'groupe'            => 'Groupe',
        'transport'         => 'Transport',
        'leave_type'        => 'Type de congé',
        'document_type'     => 'Type de document',
        'payroll'           => 'Paie',
        'declaration'       => 'Déclaration',
        'attendance'        => 'Présence',
        'contract'          => 'Contrat',
    ],

    /*
     * Labels lisibles pour les champs trackés dans le journal d'audit.
     * Clé = nom du champ en base, Valeur = label affiché.
     */
    'fields' => [
        // Employé
        'first_name'         => 'Prénom',
        'last_name'          => 'Nom',
        'status'             => 'Statut',
        'contract_type'      => 'Type de contrat',
        'profession_id'      => 'Profession',
        'email'              => 'Email',
        'phone'              => 'Téléphone',
        'phone_fixed'        => 'Téléphone fixe',
        'hire_date'          => 'Date d\'embauche',
        'exit_date'          => 'Date de sortie',
        'exit_reason'        => 'Motif de sortie',
        'exit_comment'       => 'Commentaire de sortie',
        'company_id'         => 'Entreprise',
        'gender'             => 'Genre',
        'marital_status'     => 'Situation familiale',
        'number_of_children' => 'Nombre d\'enfants',
        'address'            => 'Adresse',
        'city'               => 'Ville',
        'nationality'        => 'Nationalité',
        'rib'                => 'RIB',
        'cin'                => 'CIN',
        'cnss_number'        => 'N° CNSS',
        'matricule'          => 'Matricule',
        'birth_date'         => 'Date de naissance',
        'birth_place'        => 'Lieu de naissance',
        'diploma'            => 'Diplôme',
        'promotion'          => 'Promotion',
        'transport_id'       => 'Transport',
        'photo'              => 'Photo',
        // Congé / Demande
        'approved_by'        => 'Approuvé par',
        'approved_at'        => 'Date d\'approbation',
        'rh_notes'           => 'Notes RH',
        'start_date'         => 'Date de début',
        'end_date'           => 'Date de fin',
        'leave_type_id'      => 'Type de congé',
        'reason'             => 'Motif',
        // Demande de document
        'type'               => 'Type',
        'format'             => 'Format',
        'processed_by'       => 'Traité par',
        'processed_at'       => 'Date de traitement',
        'fichier_final'      => 'Fichier final',
        // Document employé
        'type_document'      => 'Type de document',
        'uploaded_by'        => 'Uploadé par',
        'employee_id'        => 'Employé',
        // User
        'name'               => 'Nom',
        // Groupe
        'niveau_scolaire_id' => 'Niveau scolaire',
        // Transport
        'matricule_transport' => 'Matricule',
        // Document type
        'active'             => 'Actif',
        'sort_order'         => 'Ordre',
        'code'               => 'Code',
        'categorie'          => 'Catégorie',
    ],

];

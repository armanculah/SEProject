<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/NotesDao.php';

class NotesService extends BaseService {
    private $notesDao;

    public function __construct() {
        $this->notesDao = new NotesDao();
    }

    public function getAllNotes() {
        return $this->notesDao->getAll();
    }

    public function getNoteById($noteId) {
        return $this->notesDao->getById($noteId);
    }

    public function createNote($data) {
        return $this->notesDao->insert($data);
    }

    public function updateNote($noteId, $data) {
        return $this->notesDao->update($noteId, $data);
    }

    public function deleteNote($noteId) {
        return $this->notesDao->delete($noteId);
    }
}
?>

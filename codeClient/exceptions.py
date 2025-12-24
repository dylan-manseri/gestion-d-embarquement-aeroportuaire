class WrongArgumentException(Exception):
    def __init__(self, message="Erreur d'argument, plus d'informations dans les logs"):
        super().__init__(message)
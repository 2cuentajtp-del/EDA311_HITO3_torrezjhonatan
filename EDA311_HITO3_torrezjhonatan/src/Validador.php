<?php
/** Valida y normaliza los datos enviados desde los formularios. */
class Validador
{
    /** @return array [valores limpios, errores] */
    public static function validar(array $campos, array $entrada, PDO $pdo): array
    {
        $valores = [];
        $errores = [];

        foreach ($campos as $nombre => $def) {
            $valor = trim((string)($entrada[$nombre] ?? ''));
            $etiqueta = $def['etiqueta'];

            if ($valor === '') {
                $errores[] = "El campo $etiqueta es obligatorio.";
                continue;
            }

            switch ($def['tipo']) {
                case 'texto':
                    if (mb_strlen($valor) > $def['max']) {
                        $errores[] = "$etiqueta no puede superar {$def['max']} caracteres.";
                    }
                    $valores[$nombre] = $valor;
                    break;

                case 'email':
                    if (!filter_var($valor, FILTER_VALIDATE_EMAIL) || mb_strlen($valor) > $def['max']) {
                        $errores[] = "$etiqueta no tiene un formato válido.";
                    }
                    $valores[$nombre] = $valor;
                    break;

                case 'entero':
                    if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
                        $errores[] = "$etiqueta debe ser un número entero.";
                        break;
                    }
                    $n = (int)$valor;
                    if (isset($def['min']) && $n < $def['min']) $errores[] = "$etiqueta debe ser al menos {$def['min']}.";
                    if (isset($def['max']) && $n > $def['max']) $errores[] = "$etiqueta no puede ser mayor que {$def['max']}.";
                    $valores[$nombre] = $n;
                    break;

                case 'decimal':
                    $valor = str_replace(',', '.', $valor);
                    if (!is_numeric($valor)) {
                        $errores[] = "$etiqueta debe ser un número.";
                        break;
                    }
                    if (isset($def['min']) && (float)$valor < $def['min']) $errores[] = "$etiqueta no puede ser negativo.";
                    if ((float)$valor >= 100000000) $errores[] = "$etiqueta es demasiado grande.";
                    $valores[$nombre] = number_format((float)$valor, 2, '.', '');
                    break;

                case 'fecha':
                    $f = DateTime::createFromFormat('Y-m-d', $valor);
                    if (!$f || $f->format('Y-m-d') !== $valor) {
                        $errores[] = "$etiqueta no es una fecha válida (AAAA-MM-DD).";
                    }
                    $valores[$nombre] = $valor;
                    break;

                case 'fk':
                    if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
                        $errores[] = "Selecciona un valor válido para $etiqueta.";
                        break;
                    }
                    $st = $pdo->prepare("SELECT COUNT(*) FROM {$def['ref_tabla']} WHERE {$def['ref_pk']} = ?");
                    $st->execute([(int)$valor]);
                    if ((int)$st->fetchColumn() === 0) {
                        $errores[] = "El valor seleccionado para $etiqueta no existe.";
                    }
                    $valores[$nombre] = (int)$valor;
                    break;
            }
        }
        return [$valores, $errores];
    }
}

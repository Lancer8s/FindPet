USE FindPet
GO

--CREATE
CREATE OR ALTER PROCEDURE sp_crear_aviso
  @nombre_mascota VARCHAR(100),
  @descripcion VARCHAR(500),
  @fecha_publicacion DATE = NULL,
  @urgente BIT = 0,
  @estado_salud VARCHAR(300) = NULL,
  @id_usuario INT,
  @id_ubicacion INT,
  @tipo_aviso VARCHAR(10), -- 'perdida' o 'adopcion'
  @fecha_perdida DATE = NULL,       -- Solo si es perdida
  @requisitos VARCHAR(300) = NULL,  -- Solo si es adopcion
  @donaciones BIT = 0               -- Solo si es adopcion
AS
BEGIN
  SET NOCOUNT ON;

  -- Asignar la fecha actual si no se proporciona
  IF @fecha_publicacion IS NULL
    SET @fecha_publicacion = CAST(GETDATE() AS DATE);

  -- Insertar aviso base
  INSERT INTO AVISO (nombre_mascota, descripcion, fecha_publicacion, urgente, estado_salud, id_usuario, id_ubicacion)
  VALUES (@nombre_mascota, @descripcion, @fecha_publicacion, @urgente, @estado_salud, @id_usuario, @id_ubicacion);

  DECLARE @id_aviso INT = SCOPE_IDENTITY();

  -- Insertar en tabla correspondiente según el tipo
  IF @tipo_aviso = 'perdida'
  BEGIN
    INSERT INTO AVISO_PERDIDA (fecha_perdida, id_aviso)
    VALUES (@fecha_perdida, @id_aviso);
  END
  ELSE IF @tipo_aviso = 'adopcion'
  BEGIN
    INSERT INTO AVISO_ADOPCION (requisitos, donaciones, id_aviso)
    VALUES (@requisitos, @donaciones, @id_aviso);
  END

  -- Devolver el ID del aviso creado
  SELECT @id_aviso AS id_aviso_creado;
END
GO



--READ
CREATE OR ALTER PROCEDURE sp_listar_avisos
AS
BEGIN
    SELECT 
        a.id_aviso,
        a.nombre_mascota,
        a.descripcion,
        u.direccion,
        u.latitud,
        u.longitud,
        CASE 
            WHEN p.id_perdida IS NOT NULL THEN 'perdida'
            WHEN ad.id_adopcion IS NOT NULL THEN 'adopcion'
            ELSE 'otro'
        END AS tipo
    FROM AVISO a
    JOIN UBICACION u ON a.id_ubicacion = u.id_ubicacion
    LEFT JOIN AVISO_PERDIDA p ON a.id_aviso = p.id_aviso
    LEFT JOIN AVISO_ADOPCION ad ON a.id_aviso = ad.id_aviso
END
GO
--READ: Listar avisos por usuario
CREATE PROCEDURE sp_listar_avisos_por_usuario
  @id_usuario INT
AS
BEGIN
  SELECT 
    A.id_aviso,
    A.nombre_mascota,
    A.descripcion,
    A.fecha_publicacion,
    A.urgente,
    A.estado_salud,
    UB.direccion,
    UB.latitud,
    UB.longitud
  FROM AVISO A
  INNER JOIN UBICACION UB ON A.id_ubicacion = UB.id_ubicacion
  WHERE A.id_usuario = @id_usuario;
END
GO
--UPDATE
CREATE PROCEDURE sp_actualizar_aviso
  @id_aviso INT,
  @nombre_mascota VARCHAR(100),
  @descripcion VARCHAR(500),
  @fecha_publicacion DATE,
  @urgente BIT,
  @estado_salud VARCHAR(300),
  @id_usuario INT,
  @id_ubicacion INT
AS
BEGIN
  UPDATE AVISO
  SET 
    nombre_mascota = @nombre_mascota,
    descripcion = @descripcion,
    fecha_publicacion = @fecha_publicacion,
    urgente = @urgente,
    estado_salud = @estado_salud,
    id_usuario = @id_usuario,
    id_ubicacion = @id_ubicacion
  WHERE id_aviso = @id_aviso;
END
GO

--UPDATE UBICACION DE AVISO
CREATE PROCEDURE sp_actualizar_ubicacion
  @id_ubicacion INT,
  @direccion VARCHAR(300),
  @latitud DECIMAL(10,8),
  @longitud DECIMAL(11,8)
AS
BEGIN
  UPDATE UBICACION
  SET 
    direccion = @direccion,
    latitud = @latitud,
    longitud = @longitud
  WHERE id_ubicacion = @id_ubicacion;
END
GO

--DELETE
CREATE PROCEDURE sp_eliminar_aviso
  @id_aviso INT
AS
BEGIN
  DELETE FROM AVISO WHERE id_aviso = @id_aviso;
END
GO

USE FindPet;
GO
GRANT EXECUTE TO pet;
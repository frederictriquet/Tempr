CREATE LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION triggerfunc_update_last_update_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.last_update = now(); 
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER trigger_metas_update_last_update_column BEFORE UPDATE
    ON metas FOR EACH ROW EXECUTE PROCEDURE 
    triggerfunc_update_last_update_column();
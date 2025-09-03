# 192.168.1.118
CreateViews.log: ViewDdl.sql CreateTables.log
	psql -h 82.217.5.32 -p 32781 -U cubetool -f $< >> $@

CreateTables.log: TableDdl.sql
	psql -h 82.217.5.32 -p 32781 -U cubetool -f $< >> $@

ViewDdl.sql: CubeBoModel.cgm /git/Templates/View_pg.cgt
	perl ../CubeGen.pl $< $(word 2,$^) $@ CubeTool

TableDdl.sql: CubeDbModel.cgm /git/Templates/Table_pg.cgt
	perl ../CubeGen.pl $< $(word 2,$^) $@

CubeDbModel.cgm: CubeBoModel.cgm /git/Templates/DbModel.cgt
	perl ../CubeGen.pl $< $(word 2,$^) $@ CubeTool 


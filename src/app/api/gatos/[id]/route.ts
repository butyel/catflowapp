import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

async function getGatoById(id: number, userId: number, isAdmin: boolean) {
  const where: any = { id };
  if (!isAdmin) where.userId = userId;

  const gato = await prisma.gato.findFirst({
    where,
    include: {
      ala: { select: { nome: true, id: true } },
      user: { select: { nome: true, email: true } },
      saude: { orderBy: { dataEvento: "desc" } },
      medicamentos: {
        where: { status: "ativo" },
        orderBy: { createdAt: "desc" },
      },
      pesoHistorico: { orderBy: { data: "desc" }, take: 10 },
      adocoes: { orderBy: { dataAdocao: "desc" } },
    },
  });

  return gato;
}

export async function GET(request: Request, { params }: { params: { id: string } }) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  try {
    const id = parseInt(params.id);
    const gato = await getGatoById(id, session.userId, session.userRole === "admin");

    if (!gato) {
      return NextResponse.json({ success: false, message: "Gato não encontrado" }, { status: 404 });
    }

    return NextResponse.json({
      success: true,
      data: {
        ...gato,
        peso: gato.peso ? Number(gato.peso) : null,
        saude: gato.saude.map((s) => ({ ...s })),
        medicamentos: gato.medicamentos,
        pesoHistorico: gato.pesoHistorico.map((p) => ({ ...p, peso: Number(p.peso) })),
      },
    });
  } catch (error) {
    console.error("Get gato error:", error);
    return NextResponse.json({ success: false, message: "Erro ao buscar gato" }, { status: 500 });
  }
}

export async function PUT(request: Request, { params }: { params: { id: string } }) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  try {
    const id = parseInt(params.id);
    const userId = session.userId;
    const isAdmin = session.userRole === "admin";
    const body = await request.json();

    const where: any = { id };
    if (!isAdmin) where.userId = userId;

    const existing = await prisma.gato.findFirst({ where });
    if (!existing) {
      return NextResponse.json({ success: false, message: "Gato não encontrado" }, { status: 404 });
    }

    if (body.ala_id) {
      const ala = await prisma.ala.findFirst({
        where: { id: body.ala_id, userId },
      });
      if (!ala) {
        return NextResponse.json({ success: false, message: "Ala não encontrada" }, { status: 400 });
      }
    }

    await prisma.gato.update({
      where: { id },
      data: {
        nome: body.nome,
        sexo: body.sexo,
        status: body.status,
        castrado: body.castrado ?? false,
        idade: body.idade || null,
        peso: body.peso ? parseFloat(body.peso) : null,
        raca: body.raca || null,
        dataNascimento: body.dataNascimento ? new Date(body.dataNascimento) : null,
        corPadrao: body.corPadrao || null,
        microchip: body.microchip || null,
        pedigree: body.pedigree || null,
        alaId: body.ala_id ? parseInt(body.ala_id) : null,
        doencasPreExistentes: body.doencasPreExistentes || null,
        historico: body.historico || null,
      },
    });

    return NextResponse.json({ success: true, message: "Gato atualizado com sucesso!" });
  } catch (error) {
    console.error("Update gato error:", error);
    return NextResponse.json({ success: false, message: "Erro interno ao atualizar" }, { status: 500 });
  }
}

export async function DELETE(request: Request, { params }: { params: { id: string } }) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  try {
    const id = parseInt(params.id);
    const userId = session.userId;
    const isAdmin = session.userRole === "admin";

    const where: any = { id };
    if (!isAdmin) where.userId = userId;

    const existing = await prisma.gato.findFirst({ where });
    if (!existing) {
      return NextResponse.json({ success: false, message: "Gato não encontrado" }, { status: 404 });
    }

    await prisma.gato.delete({ where: { id } });

    return NextResponse.json({ success: true, message: "Gato removido com sucesso!" });
  } catch (error) {
    console.error("Delete gato error:", error);
    return NextResponse.json({ success: false, message: "Erro interno ao remover" }, { status: 500 });
  }
}

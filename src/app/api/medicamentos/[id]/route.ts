import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function PUT(request: Request, { params }: { params: { id: string } }) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const id = parseInt(params.id);
    const body = await request.json();

    const med = await prisma.medicamento.findFirst({
      where: {
        id,
        ...(session.userRole !== "admin" ? { gato: { userId: session.userId } } : {}),
      },
    });
    if (!med) return NextResponse.json({ success: false, message: "Medicamento não encontrado" }, { status: 404 });

    await prisma.medicamento.update({
      where: { id },
      data: { status: body.status || med.status },
    });

    if (body.status === "concluido" && body.observacao) {
      await prisma.medicamentoHistorico.create({
        data: { medicamentoId: id, observacao: body.observacao },
      });
    }

    return NextResponse.json({ success: true, message: "Medicamento atualizado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao atualizar" }, { status: 500 });
  }
}

export async function DELETE(request: Request, { params }: { params: { id: string } }) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const id = parseInt(params.id);
    const med = await prisma.medicamento.findFirst({
      where: {
        id,
        ...(session.userRole !== "admin" ? { gato: { userId: session.userId } } : {}),
      },
    });
    if (!med) return NextResponse.json({ success: false, message: "Medicamento não encontrado" }, { status: 404 });

    await prisma.medicamento.delete({ where: { id } });
    return NextResponse.json({ success: true, message: "Medicamento removido!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao remover" }, { status: 500 });
  }
}
